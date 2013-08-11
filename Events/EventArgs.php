<?php

namespace Rid\Bundle\ImageBundle\Events;

use Rid\Bundle\ImageBundle\Model\RidImage;
use Symfony\Component\EventDispatcher\Event;

class EventArgs extends Event
{
    public $object;
    public $options;

    // set true to skip next event for the "PRE_..." events
    // for example if you don't want remove files in PRE_REMOVE event
    public $skipEvent = false;

    public function __construct($object, $options)
    {
        $this->object = $object;
        $this->options = $options;
    }
}
