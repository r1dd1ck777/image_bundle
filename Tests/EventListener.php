<?php

namespace Rid\Bundle\ImageBundle\Tests;

use Rid\Bundle\ImageBundle\Events\Events;
use Rid\Bundle\ImageBundle\Events\EventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_HANDLE => 'onPreHandle',
        );
    }

    public function onPreHandle(EventArgs $event)
    {
        die('eee');
    }
}