<?php

namespace Rid\Bundle\ImageBundle\Services;

use Rid\Bundle\ImageBundle\Exception\ArgumentException;
use Rid\Bundle\ImageBundle\Model\RidFile;
use Rid\Bundle\ImageBundle\Model\RidImage;

class ConfigSetter
{
    /** @var \Rid\Bundle\ImageBundle\Services\Config */
    public $config;

    /**
     * @param \Rid\Bundle\ImageBundle\Services\Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function configEntityIfNeed($entity)
    {
        if (in_array(get_class($entity), $this->config->getClassNames())) {
            $this->configEntity($entity);
        }
    }

    public function configEntity($entity)
    {
        if (!in_array(get_class($entity), $this->config->getClassNames())) {
            throw new ArgumentException("Class ".get_class($entity)." is not defined in config file.");
        }

        $class = get_class($entity);
        foreach($this->config->getFieldNamesFor($class) as $fieldName){
            $this->configEntityField($entity, $fieldName);
        }
    }

    public function configEntityField($entity, $fieldName, $preset = null)
    {
        if (is_null($preset)){
            $preset = $this->config->getPresetNameFor($entity, $fieldName);
        }

        $getMethod = 'get'.ucfirst($fieldName);
        if (!method_exists($entity, $getMethod)) throw new \Exception("RidImageBundle: method $getMethod not exists in ". get_class($entity));

        $setMethod = 'set'.ucfirst($fieldName);
        if (!method_exists($entity, $getMethod)) throw new \Exception("RidImageBundle: method $setMethod not exists in ". get_class($entity));

        /** @var $ridImage \Rid\Bundle\ImageBundle\Model\RidImage */
        $ridImage = call_user_func(array($entity , $getMethod));

        if (is_null($ridImage)) {
            $ridImage = new RidImage();
            call_user_func(array($entity , $setMethod), $ridImage);
        }

        $this->configRidFile($ridImage, $preset);
    }

    public function configRidFile(RidFile $ridImage, $preset)
    {
        $ridImage->setPreset($preset);
        $ridImage->setConfig($this->config);

        return $ridImage;
    }


}
