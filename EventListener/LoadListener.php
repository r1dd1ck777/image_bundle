<?php
namespace Rid\Bundle\ImageBundle\EventListener;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Rid\Bundle\ImageBundle\Services\Config;
use Rid\Bundle\ImageBundle\Services\RidImageManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadListener
{
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter */
    public $configSetter;

    public function  postLoad($eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $this->configSetter->configEntityIfNeed($entity);
    }
}
