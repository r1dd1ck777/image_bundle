<?php

namespace Rid\Bundle\ImageBundle\Tests\Entities;

use Doctrine\ORM\Mapping as ORM;
use Rid\Bundle\ImageBundle\Model\RidFile;
use Rid\Bundle\ImageBundle\Model\RidImage;

/**
 * @ORM\Table(name="rid_image_test_category")
 * @ORM\Entity()
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="rid_image", length=255)
     */
    protected $image;

    /**
     * @var string
     *
     * @ORM\Column(type="rid_image", length=255)
     */
    protected $ridFile;

    public function __construct()
    {
        $this->image = new RidImage();
        $this->ridFile = new RidFile();
    }

    /**
     * @param string $ridFile
     */
    public function setRidFile($ridFile)
    {
        $this->ridFile = $ridFile;
    }

    /**
     * @return string
     */
    public function getRidFile()
    {
        return $this->ridFile;
    }



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get image
     *
     * @return \Rid\Bundle\ImageBundle\Model\RidImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param rid_image $image
     * @return Category
     */
    public function setImage($image)
    {
        $this->image = $image;
    
        return $this;
    }
}