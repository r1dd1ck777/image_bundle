<?php

namespace Rid\Bundle\ImageBundle\Tests\Entities;

use Doctrine\ORM\Mapping as ORM;
use Rid\Bundle\ImageBundle\Model\RidImage;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="rid_image_test_product")
 * @ORM\Entity()
 */
class Product
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
    protected $preview;

    public function __construct()
    {
        $this->image = new RidImage();
        $this->preview = new RidImage();
    }

    //--

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
     * @return Product
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
     * Set image
     *
     * @param rid_image $image
     * @return Product
     */
    public function setImage($image)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return rid_image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set preview
     *
     * @param rid_image $preview
     * @return Product
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    
        return $this;
    }

    /**
     * Get preview
     *
     * @return rid_image 
     */
    public function getPreview()
    {
        return $this->preview;
    }
}