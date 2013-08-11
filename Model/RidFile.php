<?php

namespace Rid\Bundle\ImageBundle\Model;

class RidFile implements \ArrayAccess
{
    public function offsetSet($offset, $value) {}
    public function offsetExists($offset) {}
    public function offsetUnset($offset) {}
    public function offsetGet($offset)
    {
        return $this->__toString();
    }

    const TYPE_NONE = 1;
    const TYPE_SIMPLE_FILE = 2;

    const CONTEXT_ORIGIN = 1;
    const CONTEXT_OLD = 2;

    protected $value = '';
    protected $preset;
    /** @var \Rid\Bundle\ImageBundle\Services\Config */
    protected $config;

    // tmp data
    protected $file;
    protected $oldValue;

    public function __construct($value = '')
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->getOriginFullPath();
    }

    // system dir "/var/www/server/web/uploads/images/"
    public function getOriginDir()
    {
        return $this->config->getPresetDir($this->getPreset());
    }

    // system filename "/var/www/server/web/uploads/images/no-image.jpg"
    public function getOriginFullFileName($context = self::CONTEXT_ORIGIN)
    {
        if ($context == self::CONTEXT_ORIGIN){
            return $this->getOriginDir().$this->getValue();
        }
        if ($context == self::CONTEXT_OLD){
            return $this->getOriginDir().$this->getOldValue();
        }
    }

    public function isInit()
    {
        return !is_null($this->getPreset());
    }

    public function hasValue()
    {
        return ($this->getValue() != '') && (!is_null($this->getValue()));
    }

    public function isReady()
    {
        return $this->isInit() && $this->hasValue();
    }

    // web path "uploads/images/"
    public function getOriginPath()
    {
        return $this->config->getPresetPath($this->getPreset());
    }

    // web filename "uploads/images/no-image.jpg"
    public function getOriginFullPath($context = self::CONTEXT_ORIGIN)
    {
        if ($context == self::CONTEXT_ORIGIN){
            return $this->getOriginPath().$this->getValue();
        }
        if ($context == self::CONTEXT_OLD){
            return $this->getOriginPath().$this->getOldValue();
        }
    }


    public function getType()
    {
        if (!is_null($this->getFile())){
            return self::TYPE_SIMPLE_FILE;
        }

        return self::TYPE_NONE;
    }

    //set get
    public function setConfig(&$config)
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setPreset($preset)
    {
        $this->preset = $preset;

        return $this;
    }

    public function getPreset()
    {
        return $this->preset;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function clearFile()
    {
        $this->file = null;

        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    public function getOldValue()
    {
        return $this->oldValue;
    }

    public function generateName($extension = null, $name = null)
    {
        $this->setOldValue($this->getValue());
        $this->setValue(($name ?: self::generateSha1()). ( $extension ? '.'.$extension : ''));

        return $this->getValue();
    }

    public static function generateSha1()
    {
        return sha1(uniqid(mt_rand(), true));
    }

}
