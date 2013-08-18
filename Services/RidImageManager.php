<?php


namespace Rid\Bundle\ImageBundle\Services;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Rid\Bundle\ImageBundle\Events\EventArgs;
use Rid\Bundle\ImageBundle\Events\Events;
use Rid\Bundle\ImageBundle\Exception\ArgumentException;
use Rid\Bundle\ImageBundle\Model\RidFile;
use Rid\Bundle\ImageBundle\Model\RidImage;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RidImageManager
{
    /** @var \Imagine\Image\ImageInterface */
    protected $imagine;
    /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    protected $dispatcher;
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter $config */
    protected $configSetter;

    public function setConfigSetter($configSetter)
    {
        $this->configSetter = $configSetter;
    }

    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function setImagine($imagine)
    {
        $this->imagine = $imagine;
    }

    public function handle($object, $preset = null)
    {
        if ($object instanceof RidFile){
            if (is_scalar($preset)){
                $this->configSetter->configRidFile($object, $preset);
            }else{
                if (!$object->isInit()){
                    throw new ArgumentException("Object must be configured or your should define a 'preset'.");
                }
            }
        }

        if ($object instanceof RidImage){
            $this->handleRidImage($object);
        }
        elseif($object instanceof RidFile){
            $this->handleRidFile($object);
        }
        else{
            $this->handleEntity($object);
        }
    }

    public function handleEntity($object)
    {
        $this->configSetter->configEntity($object);
        $class = get_class($object);
        foreach($this->configSetter->config->getFieldNamesFor($class) as $field){
            $getter = 'get'.ucfirst($field);
            $ridFile = $object->$getter();

            if ($ridFile instanceof RidImage){
                $this->handleRidImage($ridFile, array('entity' => $object, 'field' =>$field ));
            }
            elseif($ridFile instanceof RidFile){
                $this->handleRidFile($ridFile, array('entity' => $object, 'field' =>$field ));
            }

            var_dump($ridFile);
        }
    }

    public function handleRidImage(RidImage $ridImage, array $options = array())
    {
        $eventArgs = new EventArgs($ridImage, $options);
        $this->dispatcher->dispatch(Events::PRE_HANDLE, $eventArgs);

        if (!$eventArgs->skipEvent){
            $this->doHandleRidImage($ridImage, $options);
        }

        $eventArgs->skipEvent = false;
        $this->dispatcher->dispatch(Events::POST_HANDLE, $eventArgs);
    }

    public function handleRidFile(RidFile $ridFile, array $options = array())
    {
        $eventArgs = new EventArgs($ridFile, $options);
        $this->dispatcher->dispatch(Events::PRE_HANDLE, $eventArgs);

        if (!$eventArgs->skipEvent){
            $this->doHandleRidFile($ridFile, $options);
        }

        $eventArgs->skipEvent = false;
        $this->dispatcher->dispatch(Events::POST_HANDLE, $eventArgs);
    }

    public function doHandleRidImage(RidImage $ridImage)
    {
        if ($ridImage->getType() == RidImage::TYPE_SIMPLE_FILE){
            $this->doHandleRidFile($ridImage);
            $this->createThumbnails($ridImage);
        }
    }

    public function doHandleRidFile(RidFile $ridFile)
    {
        if ($ridFile->getType() == RidImage::TYPE_SIMPLE_FILE){
            $file = $ridFile->getFile();
            $filename = $ridFile->generateName($file->guessExtension());
            $file->move($ridFile->getOriginDir() , $filename);
            $this->removeFiles($ridFile, RidFile::CONTEXT_OLD);
            $ridFile->clearFile();
        }
    }

    // $object - RidImage or RidFile
    public function removeFiles($object, $context = RidImage::CONTEXT_ORIGIN, $options=array())
    {
        $eventArgs = new EventArgs($object, $options);
        $this->dispatcher->dispatch(Events::PRE_HANDLE_REMOVE, $eventArgs);

        if (!$eventArgs->skipEvent){
            $this->doRemoveFiles($object, $context);
        }

        $eventArgs = new EventArgs($object, $options);
        $this->dispatcher->dispatch(Events::POST_HANDLE_REMOVE, $eventArgs);
    }

    public function doRemoveFiles(RidFile $object, $context = RidImage::CONTEXT_ORIGIN, $options=array())
    {
        if (!$object->isInit()){
            throw new ArgumentException("Object must be configured or your should define a 'preset'.");
        }

        $file = $object->getOriginFullFileName($context);
        if (is_file($file)){
            unlink($file);
        }

        if ($object instanceof RidImage){
            $this->removeThumbnails($object, $context);
        }
    }

    public function createThumbnails(RidImage $ridImage)
    {
        foreach($ridImage->getThumbnailNames() as $thumbnailName){
            $data = $ridImage->getThumbnailData($thumbnailName);
            $thumbnailType = (isset($data['type']) && $data['type'] == 'inset') ? ImageInterface::THUMBNAIL_INSET : ImageInterface::THUMBNAIL_OUTBOUND;

            $image = $this->imagine->open( $ridImage->getOriginFullFileName() );
            $thumbnail = $image->thumbnail(new Box($data['width'], $data['height']), $thumbnailType);
            $thumbnail->save($ridImage->getThumbnailFullFileName($thumbnailName));
        }
    }

    public function removeThumbnails(RidImage $ridImage, $context = RidImage::CONTEXT_ORIGIN)
    {
        foreach($ridImage->getThumbnailNames() as $thumbnailName){
            $file = $ridImage->getThumbnailFullFileName($thumbnailName, $context);
            if (is_file($file)){
                unlink($file);
            }
        }
    }

    //config
    /**
     * arguments:
     * entity
     * ridFile
     * ridFile, preset
     */
    public function config($object, $preset=null)
    {
        if ($object instanceof RidFile){
            if (is_null($preset)){
                throw new ArgumentException("Second parameter 'preset' should be defined in this case.");
            }
            $this->configSetter->configRidFile($object, $preset);
        }else{
            $this->configSetter->configEntity($object);
        }
    }
}
