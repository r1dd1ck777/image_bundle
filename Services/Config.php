<?php

namespace Rid\Bundle\ImageBundle\Services;

class Config
{
    protected $config;

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getClasses()
    {
        return $this->config['fields'];
    }

    public function getClassNames()
    {
        $c = $this->getClasses();
        return array_keys($c);
    }

    public function getFieldsFor($class)
    {
        return $this->config['fields'][$class];
    }

    public function getFieldNamesFor($class)
    {
        $f = $this->getFieldsFor($class);
        return array_keys($f);
    }

    public function getPresetNameFor($class, $field)
    {
        if (is_object($class)){
            $class = get_class($class);
        }
        $f = $this->getFieldsFor($class);

        return $f[$field];
    }

    public function getDirWeb()
    {
        return $this->config['dir']['web'];
    }

    public function getPathTmp()
    {
        return $this->config['path']['tmp'];
    }

    public function getPreset($presetName)
    {
        return $this->config['presets'][$presetName];
    }

    public function getPresetPath($presetName)
    {
        $p = $this->getPreset($presetName);
        return $p['path'];
    }

    public function getThumbnails($presetName)
    {
        $p = $this->getPreset($presetName);
        return $p['thumbnails'];
    }

    public function getThumbnail($presetName, $thumbnailName)
    {
        $t = $this->getThumbnails($presetName);
        return $t[$thumbnailName];
    }

    public function getThumbnailNames($presetName)
    {
        $t = $this->getThumbnails($presetName);
        return array_keys($t);
    }

    public function getPresetDir($presetName)
    {
        return $this->getDirWeb() . $this->getPresetPath($presetName);
    }

    public function getDefaultFullPath($presetName)
    {
        $p = $this->getPreset($presetName);
        return isset($p['default']) ? $p['default'] : null;
    }

    public function getThumbnailDefaultFullPath($presetName, $thumbnailName)
    {
        $t = $this->getThumbnail($presetName, $thumbnailName);
        return isset($t['default']) ? $t['default'] : $this->getDefaultFullPath($presetName);
    }
}
