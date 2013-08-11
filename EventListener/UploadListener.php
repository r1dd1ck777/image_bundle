<?php
namespace Rid\Bundle\ImageBundle\EventListener;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
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

    public function preFlush(EventArgs $ea)
    {
        $this->config = $this->container->get('rid.image.config');
        $this->ridImageManager = $this->container->get('rid_image');
        $this->configSetter = $this->container->get('rid.image.config_setter');

        /** @var \Doctrine\ORM\EntityManager $om */
        $om = $ea->getEntityManager();
        /** @var \Doctrine\ORM\UnitOfWork $uow */
        $uow = $om->getUnitOfWork();

        $identityMap = $uow->getIdentityMap();
        foreach(array_keys($identityMap) as $class){
            if (in_array($class, $this->config->getClassNames())){
                $this->scheduleForUpdate($identityMap[$class], $class, $uow);
            }
        }
    }

    protected function scheduleForUpdate($entities, $class, UnitOfWork $uow)
    {
        foreach($entities as $entity){
            foreach($this->config->getFieldNamesFor($class) as $field){
                $getter = 'get'.ucfirst($field);
                $ridImage = $entity->$getter();
                $uow->propertyChanged($entity, $field, $ridImage, $ridImage);
            }
            $uow->scheduleForUpdate($entity);
        }
    }

    public function onFlush(EventArgs $ea)
    {
        /** @var \Doctrine\ORM\EntityManager $om */
        $om = $ea->getEntityManager();
        /** @var \Doctrine\ORM\UnitOfWork $uow */
        $uow = $om->getUnitOfWork();

        $toSave = $uow->getScheduledEntityInsertions();
        $toSave = array_merge($toSave, $uow->getScheduledEntityUpdates());

        foreach ($toSave as $entity){
            if (in_array(get_class($entity), $this->config->getClassNames())){
                $this->scheduledUploads[]= $entity;
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
        foreach($this->scheduledRemoves as $entity)
        {
            $this->configSetter->configEntityIfNeed($entity);
            $class = get_class($entity);
            foreach($this->config->getFieldNamesFor($class) as $field){
                $getter = 'get'.ucfirst($field);
                $ridImage = $entity->$getter();
                $this->ridImageManager->handleRemovingRidImage($ridImage, $entity, $field);
            }
        }
    }
}
