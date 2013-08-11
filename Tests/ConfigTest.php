<?php

namespace Rid\Bundle\ImageBundle\Tests;

use Rid\Bundle\ImageBundle\RidImageTestCase;

class ConfigTest extends RidImageTestCase
{
    /** @var \Rid\Bundle\ImageBundle\Services\Config $config */
    protected $config;
    
    public function setUp()
    {
        $this->init();
        $this->config = $this->_container->get('rid.image.config');
    }

    public function testClasses()
    {
        $classes = $this->config->getClasses();
        $this->assertEquals(true, in_array('Rid\Bundle\ImageBundle\Tests\Entities\Category', array_keys($classes)));
        $this->assertEquals(true, in_array('Rid\Bundle\ImageBundle\Tests\Entities\Product', array_keys($classes)));
    }

    public function testClassNames()
    {
        $classes = $this->config->getClassNames();
        $this->assertEquals(true, in_array('Rid\Bundle\ImageBundle\Tests\Entities\Category', $classes));
        $this->assertEquals(true, in_array('Rid\Bundle\ImageBundle\Tests\Entities\Product', $classes));
    }

    public function testFields()
    {
        $fields = $this->config->getFieldsFor('Rid\Bundle\ImageBundle\Tests\Entities\Product');
        $this->assertEquals(array('image','preview'), array_keys($fields));
    }

    public function testFieldNames()
    {
        $fields = $this->config->getFieldNamesFor('Rid\Bundle\ImageBundle\Tests\Entities\Product');
        $this->assertEquals(array('image','preview'), $fields);
    }

    public function testPresetName()
    {
        $presetName = $this->config->getPresetNameFor('Rid\Bundle\ImageBundle\Tests\Entities\Product', 'preview');
        $this->assertEquals('other_preset_name', $presetName);
    }

    public function testDirWeb()
    {
        $dir = $this->_container->getParameter("kernel.root_dir"). '/../web/';
        $this->assertEquals($dir, $this->config->getDirWeb());
    }

    public function testPathTmp()
    {
        $this->assertEquals('uploads/tmp/images/', $this->config->getPathTmp());
    }

    public function testPreset()
    {
        $preset = $this->config->getPreset('other_preset_name');
        $this->assertEquals(true, is_array($preset));

        $this->assertEquals('uploads/product/avatars/', $this->config->getPresetPath('other_preset_name'));

        $this->assertEquals($this->config->getDirWeb() . 'uploads/product/avatars/', $this->config->getPresetDir('other_preset_name'));
    }

    public function testThumbnailNames()
    {
        $names = $this->config->getThumbnailNames('other_preset_name');
        $this->assertEquals(array('small', 'big'), $names);
    }

    public function testThumbnails()
    {
        $thumbnails = $this->config->getThumbnails('other_preset_name');
        $this->assertEquals(array('small', 'big'), array_keys($thumbnails));

        $thumbnail = $this->config->getThumbnail('other_preset_name', 'small');
        $this->assertEquals(true, is_array($thumbnail));
        $this->assertEquals(true, (
            isset($thumbnail['default']) && isset($thumbnail['width']) && isset($thumbnail['height']) && isset($thumbnail['type'])
        ));
        $this->assertEquals('img/bg-product-noimage-96.jpg', $thumbnail['default']);
    }

    public function testDefaultFullPath()
    {
        $this->assertEquals('img/bg-noimage-96.jpg', $this->config->getDefaultFullPath('some_preset_name'));
    }

    public function testThumbnailDefaultFullPath()
    {
        $this->assertEquals('img/bg-category-noimage-96.jpg', $this->config->getThumbnailDefaultFullPath('some_preset_name', 'some_thumbnail_name'));
    }
}
