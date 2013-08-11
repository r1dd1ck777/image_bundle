<?php

namespace Rid\Bundle\ImageBundle\Tests;

use Rid\Bundle\ImageBundle\Model\RidImage;
use Rid\Bundle\ImageBundle\RidImageTestCase;
use Rid\Bundle\ImageBundle\Tests\Entities\Category;
use Symfony\Component\HttpFoundation\File\File;

class ORMTest extends RidImageTestCase
{
    /** @var \Rid\Bundle\ImageBundle\Services\ConfigSetter $config */
    protected $configSetter;

    /** @var \Rid\Bundle\ImageBundle\Model\RidImage $config */
    protected $ridImage;

    /** @var \Rid\Bundle\ImageBundle\Tests\Entities\Category */
    protected $category;
    protected $id;
    
    public function setUp()
    {
        $this->init();
        $this->configSetter = $this->_container->get('rid.image.config_setter');
    }

    public function testPersistLoad()
    {
//        $this->category = new Category();
//        $this->configSetter->configEntityIfNeed($this->category);
//
//        $path = realpath(__DIR__ . '/fixtures/');
//        $path2 = $path.'/source.jpg';
//        copy($path.'/image.jpeg' , $path2);
//        $file = new File($path2);
//        $this->category->setTitle('')->getImage()->setFile($file);
//        $this->_em->persist($this->category);
//        $this->_em->flush();
//        $this->_em->clear();
//        $id = $this->category->getId();
//
//        $this->assertTrue(is_file($this->category->getImage()->getOriginFullFileName()));
//        unset($this->category);
//        $this->assertFalse(isset($this->category));
//
//        $this->category = $this->_em->find('Rid\Bundle\ImageBundle\Tests\Entities\Category', $id);
//        $this->assertInstanceOf('Rid\Bundle\ImageBundle\Model\RidImage', $this->category->getImage());
//        $this->assertInstanceOf('Rid\Bundle\ImageBundle\Model\RidFile', $this->category->getRidFile());
//        $this->assertInstanceOf('Rid\Bundle\ImageBundle\Services\Config', $this->category->getRidFile()->getConfig());
    }

}
