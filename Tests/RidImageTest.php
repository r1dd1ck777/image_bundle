<?php

namespace Rid\Bundle\ImageBundle\Tests;

use Rid\Bundle\ImageBundle\Model\RidImage;
use Rid\Bundle\ImageBundle\RidImageTestCase;
use Rid\Bundle\ImageBundle\Tests\Entities\Category;

class RidImageTest extends RidImageTestCase
{
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter $config */
    protected $configSetter;

    /** @var \Rid\Bundle\ImageBundle\Model\RidImage $config */
    protected $ridImage;
    
    public function setUp()
    {
        $this->init();
        $this->configSetter = $this->_container->get('rid.image.config_setter');

        $category = new Category();
        $this->configSetter->configEntityIfNeed($category);
        $category->getImage()->setOldValue('old-image.jpg');
        $this->ridImage = $category->getImage()->setValue('no-image.jpg');
    }

    public function testThumbnailFilename()
    {
        $this->assertEquals('small_no-image.jpg', $this->ridImage->getThumbnailFileName('small'));
        $this->assertEquals('small_old-image.jpg', $this->ridImage->getThumbnailFileName('small',RidImage::CONTEXT_OLD));
    }

    public function testFullPath()
    {
        $this->assertEquals('uploads/category/small_no-image.jpg' , $this->ridImage->getThumbnailFullPath('small'));
        $this->assertEquals('uploads/category/small_old-image.jpg' , $this->ridImage->getThumbnailFullPath('small',RidImage::CONTEXT_OLD));
    }

    public function testThumbnailFullFileName()
    {
        $dir = $this->_container->getParameter("kernel.root_dir"). '/../web/';
        $this->assertEquals($dir.'uploads/category/small_no-image.jpg' , $this->ridImage->getThumbnailFullFileName('small'));
        $this->assertEquals($dir.'uploads/category/small_old-image.jpg' , $this->ridImage->getThumbnailFullFileName('small',RidImage::CONTEXT_OLD));
    }

    public function testThumbnailNames()
    {
        $names = $this->ridImage->getThumbnailNames();
        $this->assertEquals(true , is_array($names));
        $this->assertEquals(true , in_array('some_thumbnail_name' ,$names));
        $this->assertEquals(true , in_array('tiny' ,$names));
    }

    public function testThumbnailData()
    {
        $thumbnail = $this->ridImage->getThumbnailData('some_thumbnail_name');
        $this->assertEquals(true, is_array($thumbnail));
        $this->assertEquals(true, (
            isset($thumbnail['default']) && isset($thumbnail['width']) && isset($thumbnail['height']) && isset($thumbnail['type'])
        ));
        $this->assertEquals('img/bg-category-noimage-96.jpg', $thumbnail['default']);
    }

    public function testThumbnailCall()
    {
        $this->assertEquals('uploads/category/tiny_no-image.jpg', (string)$this->ridImage->tiny());
    }

    public function testThumbnailGet()
    {
        $this->assertEquals('uploads/category/tiny_no-image.jpg', (string)$this->ridImage->tiny);
    }

    public function testInitAndDefaults()
    {
        $ridImage = new RidImage();
        $this->assertFalse($ridImage->isReady());
        $this->assertFalse($ridImage->isInit());
        $this->assertFalse($ridImage->hasValue());
        $this->assertEquals('images/no-image.png', (string)$ridImage);
        $this->assertEquals('images/no-image.png', (string)$ridImage->tiny);
        $this->configSetter->configRidFile($ridImage, 'min_preset');
        $this->assertEquals('images/no-image.png', (string)$ridImage);
        $this->assertEquals('images/no-image.png', (string)$ridImage->small);

        $ridImage = new RidImage();
        $this->configSetter->configRidFile($ridImage, 'some_preset_name');
        $this->assertFalse($ridImage->isReady());
        $this->assertTrue($ridImage->isInit());
        $this->assertFalse($ridImage->hasValue());
        $this->assertEquals('img/bg-noimage-96.jpg', (string)$ridImage);
        $this->assertEquals('img/bg-category-noimage-30.jpg', (string)$ridImage->tiny);

        $ridImage = new RidImage();
        $ridImage->setValue('some-image.png');
        $this->configSetter->configRidFile($ridImage, 'some_preset_name');
        $this->assertTrue($ridImage->isReady());
        $this->assertTrue($ridImage->isInit());
        $this->assertTrue($ridImage->hasValue());
        $this->assertEquals('uploads/category/some-image.png', (string)$ridImage);
        $this->assertEquals('uploads/category/tiny_some-image.png', (string)$ridImage->tiny);
    }

    public function testArrayAccess()
    {
        $this->assertEquals('uploads/category/no-image.jpg', (string)$this->ridImage[0]);
        $this->assertEquals('uploads/category/tiny_no-image.jpg', (string)$this->ridImage['tiny']);
    }

}
