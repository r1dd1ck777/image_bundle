<?php

namespace Rid\Bundle\ImageBundle\Tests;

use Rid\Bundle\ImageBundle\Model\RidFile;
use Rid\Bundle\ImageBundle\RidImageTestCase;

class RidFileTest extends RidImageTestCase
{
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter $config */
    protected $configSetter;

    /** @var \Rid\Bundle\ImageBundle\Model\RidFile $config */
    protected $ridFile;
    
    public function setUp()
    {
        $this->init();
        $this->configSetter = $this->_container->get('rid.image.config_setter');

        $this->ridFile = new RidFile('no-image.jpg');
        $this->configSetter->configRidFile($this->ridFile, 'file_preset');
        $this->ridFile->setOldValue('old-image.jpg');
    }

    public function testOriginPath()
    {
        $this->assertEquals('uploads/files/' , $this->ridFile->getOriginPath());
    }

    public function testOriginFullPath()
    {
        $this->assertEquals('uploads/files/no-image.jpg' , $this->ridFile->getOriginFullPath());
        $this->assertEquals('uploads/files/old-image.jpg' , $this->ridFile->getOriginFullPath(ridFile::CONTEXT_OLD));
    }

    public function testOriginDir()
    {
        $dir = $this->_container->getParameter("kernel.root_dir"). '/../web/';
        $this->assertEquals($dir.'uploads/files/' , $this->ridFile->getOriginDir());
    }

    public function testOriginFullFileName()
    {
        $dir = $this->_container->getParameter("kernel.root_dir"). '/../web/';
        $this->assertEquals($dir.'uploads/files/no-image.jpg' , $this->ridFile->getOriginFullFileName());
        $this->assertEquals($dir.'uploads/files/old-image.jpg' , $this->ridFile->getOriginFullFileName(ridFile::CONTEXT_OLD));
    }

    public function testToString()
    {
        $this->assertEquals('uploads/files/no-image.jpg', (string)$this->ridFile);
    }

    public function testInitAndDefaults()
    {
        $ridFile = new RidFile();
        $this->assertFalse($ridFile->isReady());
        $this->assertFalse($ridFile->isInit());
        $this->assertFalse($ridFile->hasValue());
        $this->assertEquals('', (string)$ridFile->getValue());

        $ridFile = new RidFile();
        $this->configSetter->configRidFile($ridFile, 'file_preset');
        $this->assertFalse($ridFile->isReady());
        $this->assertTrue($ridFile->isInit());
        $this->assertFalse($ridFile->hasValue());
        $this->assertEquals('', (string)$ridFile->getValue());

        $ridFile = new RidFile();
        $ridFile->setValue('some-file.zip');
        $this->configSetter->configRidFile($ridFile, 'file_preset');
        $this->assertTrue($ridFile->isReady());
        $this->assertTrue($ridFile->isInit());
        $this->assertTrue($ridFile->hasValue());
        $this->assertEquals('uploads/files/some-file.zip', (string)$ridFile);
    }

    public function testArrayAccess()
    {
        $this->assertEquals('uploads/files/no-image.jpg', (string)$this->ridFile[0]);
    }

}
