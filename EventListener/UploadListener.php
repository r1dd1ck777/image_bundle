<?php
namespace Rid\Bundle\ImageBundle\EventListener;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Rid\Bundle\ImageBundle\Model\RidImage;
use Rid\Bundle\ImageBundle\Services\Config;
use Rid\Bundle\ImageBundle\Services\RidImageManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UploadListener implements ContainerAwareInterface
{
    protected $container;
    protected $scheduledUploads = array();
    protected $scheduledRemoves = array();
    /** @var \Rid\Bundle\ImageBundle\Services\RidImageManager */
    protected $ridImageManager;
    /** @var \Rid\Bundle\ImageBundle\Services\Config */
    protected $config;
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter */
    protected $configSetter;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function init()
    {
        $this->config = $this->container->get('rid.image.config');
        $this->ridImageManager = $this->container->get('rid_image');
        $this->configSetter = $this->container->get('rid.image.config_setter');
    }

    public function preFlush(EventArgs $ea)
    {
        $this->init();
        if ($this->ridImageManager->ignorePreFlush){return;}

        /** @var \Doctrine\ORM\EntityManager $om */
        $om = $ea->getEntityManager();
        /** @var \Doctrine\ORM\UnitOfWork $uow */
        $uow = $om->getUnitOfWork();

        $identityMap = $uow->getIdentityMap();
        foreach(array_keys($identityMap) as $class){
            if (in_array($class, $this->config->getClassNames())){
                $this->scheduleForUpdateEntities($identityMap[$class], $class, $uow);
            }
        }
    }

    protected function scheduleForUpdateEntities($entities, $class, UnitOfWork $uow)
    {
        foreach($entities as $entity){
            $needUpdate = $this->scheduleForUpdateFields($entity, $class, $uow);
            if ($needUpdate) {
                $uow->scheduleForUpdate($entity);
            }
        }
    }

    protected function scheduleForUpdateFields($entity, $class, UnitOfWork $uow)
    {
        $needUpdate = false;

        foreach($this->config->getFieldNamesFor($class) as $field){
            $getter = 'get'.ucfirst($field);
            /** @var \Rid\Bundle\ImageBundle\Model\RidImage $ridImage */
            $ridImage = $entity->$getter();
            if ($ridImage->getType() !== RidImage::TYPE_NONE ){
                $needUpdate = true;
                $uow->propertyChanged($entity, $field, $ridImage, $ridImage);
            }
        }
        return $needUpdate;
    }

    public function onFlush(EventArgs $ea)
    {
        $this->init();
        /** @var \Doctrine\ORM\EntityManager $om */
        $om = $ea->getEntityManager();
        /** @var \Doctrine\ORM\UnitOfWork $uow */
        $uow = $om->getUnitOfWork();

        $toSave = $uow->getScheduledEntityInsertions();
        $toSave = array_merge($toSave, $uow->getScheduledEntityUpdates());

        foreach ($toSave as $entity){
            if (in_array(get_class($entity), $this->config->getClassNames())){
                $this->scheduledUploads[]= $entity;
                $this->scheduleForUpdateFields($entity, get_class($entity), $uow);
            }
        }

        $this->scheduledRemoves = $uow->getScheduledEntityDeletions();
        $this->processUploads();
    }

    public function processUploads()
    {
        foreach($this->scheduledUploads as $entity)
        {
            $this->ridImageManager->handleEntity($entity);
        }
    }

    public function postFlush(EventArgs $ea)
    {
        $this->init();
        foreach($this->scheduledRemoves as $entity)
        {
            if (!in_array(get_class($entity), $this->config->getClassNames())){
                continue;
            }
            $this->configSetter->configEntityIfNeed($entity);
            $class = get_class($entity);
            foreach($this->config->getFieldNamesFor($class) as $field){
                $getter = 'get'.ucfirst($field);
                $ridImage = $entity->$getter();
                $this->ridImageManager->removeFiles($ridImage, null, array('entity' => $entity, 'field' => $field));
            }
        }
    }
}
