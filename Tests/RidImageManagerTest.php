<?php

namespace Rid\Bundle\ImageBundle\Tests;

use Rid\Bundle\ImageBundle\Model\RidFile;
use Rid\Bundle\ImageBundle\Model\RidImage;
use Rid\Bundle\ImageBundle\RidImageTestCase;
use Rid\Bundle\ImageBundle\Tests\Entities\Category;
use Symfony\Component\HttpFoundation\File\File;

class RidImageManagerTest extends RidImageTestCase
{
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter $configSetter */
    protected $configSetter;

    /** @var \Rid\Bundle\ImageBundle\Services\RidImageManager */
    protected $ridImageManager;

    /** @var \Rid\Bundle\ImageBundle\Model\RidImage */
    protected $ridImage;

    /** @var \Rid\Bundle\ImageBundle\Model\RidFile */
    protected $ridFile;

    /** @var \Rid\Bundle\ImageBundle\Tests\Entities\Category */
    protected $category;

    public function setUp()
    {
        $this->init();
        $this->configSetter = $this->_container->get('rid.image.config_setter');
        $this->ridImageManager = $this->_container->get('rid_image');
        $this->category = new Category();
        $this->configSetter->configEntity($this->category);
    }

    public function testUploadImage()
    {
        $path = realpath(__DIR__ . '/fixtures/');
        $path2 = $path.'/source.jpg';

        copy($path.'/image.jpeg' , $path2);
        $file = new File($path2);
        $this->ridImage = $this->category->getImage()->setFile($file);
        $this->ridImageManager->handle($this->ridImage);
        $this->assertTrue(is_file($this->ridImage->getOriginFullFileName()));
        $this->assertTrue(is_file($this->ridImage->getThumbnailFullFileName('some_thumbnail_name')));
        $this->assertTrue(is_file($this->ridImage->getThumbnailFullFileName('tiny')));

        $this->assertFalse(is_file($this->ridImage->getOriginFullFileName(RidImage::CONTEXT_OLD)));
        $this->assertFalse(is_file($this->ridImage->getThumbnailFullFileName('some_thumbnail_name', RidImage::CONTEXT_OLD)));
        $this->assertFalse(is_file($this->ridImage->getThumbnailFullFileName('tiny', RidImage::CONTEXT_OLD)));

        copy($path.'/image.jpeg' , $path2);
        $file = new File($path2);
        $this->ridImage = $this->category->getImage()->setFile($file);
        $this->ridImageManager->handle($this->ridImage);
        $this->assertTrue(is_file($this->ridImage->getOriginFullFileName()));
        $this->assertTrue(is_file($this->ridImage->getThumbnailFullFileName('some_thumbnail_name')));
        $this->assertTrue(is_file($this->ridImage->getThumbnailFullFileName('tiny')));

        $this->assertFalse(is_file($this->ridImage->getOriginFullFileName(RidImage::CONTEXT_OLD)));
        $this->assertFalse(is_file($this->ridImage->getThumbnailFullFileName('some_thumbnail_name', RidImage::CONTEXT_OLD)));
        $this->assertFalse(is_file($this->ridImage->getThumbnailFullFileName('tiny', RidImage::CONTEXT_OLD)));

        $this->ridImageManager->removeFiles($this->ridImage);
        $this->assertFalse(is_file($this->ridImage->getOriginFullFileName()));
        $this->assertFalse(is_file($this->ridImage->getThumbnailFullFileName('some_thumbnail_name')));
        $this->assertFalse(is_file($this->ridImage->getThumbnailFullFileName('tiny')));
    }

    public function testUploadFile()
    {
        $path = realpath(__DIR__ . '/fixtures/');
        $path2 = $path.'/source.zip';

        copy($path.'/image.jpeg.zip' , $path2);
        $file = new File($path2);
        $this->ridFile = $this->category->getRidFile()->setFile($file);
        $this->ridImageManager->handle($this->ridFile);

        $this->assertTrue(is_file($this->ridFile->getOriginFullFileName()));
        $this->assertFalse(is_file($this->ridFile->getOriginFullFileName(RidImage::CONTEXT_OLD)));

        copy($path.'/image.jpeg' , $path2);
        $file = new File($path2);
        $this->ridFile = $this->category->getImage()->setFile($file);
        $this->ridImageManager->handle($this->ridFile);

        $this->assertTrue(is_file($this->ridFile->getOriginFullFileName()));
        $this->assertFalse(is_file($this->ridFile->getOriginFullFileName(RidImage::CONTEXT_OLD)));

        $this->ridImageManager->removeFiles($this->ridFile);
        $this->assertFalse(is_file($this->ridFile->getOriginFullFileName()));
    }

    public function testConfig()
    {
        $category = new Category();

        $this->ridImageManager->config($category);
        $this->assertTrue($category->getImage()->isInit());
        $this->assertTrue($category->getRidFile()->isInit());

        $ridFile = new RidFile();
        $this->assertFalse($ridFile->isInit());
        $this->ridImageManager->config($ridFile, 'file_preset');
        $this->assertTrue($ridFile->isInit());

        $ridImage = new RidImage();
        $this->assertFalse($ridImage->isInit());
        $this->ridImageManager->config($ridImage, 'some_preset_name');
        $this->assertTrue($ridImage->isInit());
    }

    /**
     * @expectedException \Rid\Bundle\ImageBundle\Exception\ArgumentException
     */
    public function testConfigException()
    {
        $ridFile = new RidFile();
        $this->ridImageManager->config($ridFile);
    }
}
