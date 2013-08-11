<?php

namespace Rid\Bundle\ImageBundle\Tests;

use Rid\Bundle\ImageBundle\Model\RidImage;
use Rid\Bundle\ImageBundle\RidImageTestCase;
use Rid\Bundle\ImageBundle\Tests\Entities\Category;

class ConfigSetterTest extends RidImageTestCase
{
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter $config */
    protected $configSetter;

    /** @var \Rid\Bundle\ImageBundle\Model\RidImage $config */
    protected $ridImage;

    /** @var \Rid\Bundle\ImageBundle\Tests\Entities\Category */
    protected $category;
    
    public function setUp()
    {
        $this->init();
        $this->configSetter = $this->_container->get('rid.image.config_setter');
    }

    public function testConfigEntity()
    {
        $this->category = new Category();
        $this->configSetter->configEntityIfNeed($this->category);
        $this->assertEquals('some_preset_name' , $this->category->getImage()->getPreset());
        $this->assertInstanceOf('\Rid\Bundle\ImageBundle\Services\Config' , $this->category->getImage()->getConfig());

        // not throwing exception test
        $this->configSetter->configEntityIfNeed($this);
    }

    /**
     * @expectedException \Rid\Bundle\ImageBundle\Exception\ArgumentException
     */
    public function testConfigException()
    {
        $this->configSetter->configEntity($this);
    }

    public function testConfigEntityField()
    {
        $this->category = new Category();
        $this->configSetter->configEntityField($this->category, 'image');
        $this->assertEquals('some_preset_name' , $this->category->getImage()->getPreset());
        $this->assertInstanceOf('\Rid\Bundle\ImageBundle\Services\Config' , $this->category->getImage()->getConfig());
    }

    public function testConfigRidImage()
    {
        $this->ridImage = new RidImage();
        $this->configSetter->configRidFile($this->ridImage, 'some_preset_name');
        $this->assertEquals('some_preset_name' , $this->ridImage->getPreset());
        $this->assertInstanceOf('\Rid\Bundle\ImageBundle\Services\Config' , $this->ridImage->getConfig());
        $this->assertEquals('uploads/category/' , $this->ridImage->getOriginPath());

        $this->configSetter->configRidFile($this->ridImage, 'other_preset_name');
        $this->assertEquals('other_preset_name' , $this->ridImage->getPreset());
        $this->assertInstanceOf('\Rid\Bundle\ImageBundle\Services\Config' , $this->ridImage->getConfig());
        $this->assertEquals('uploads/product/avatars/' , $this->ridImage->getOriginPath());
    }
}
