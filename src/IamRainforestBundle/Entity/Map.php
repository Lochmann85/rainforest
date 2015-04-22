<?php

namespace IamRainforestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Map
 */
class Map
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $numberX;

    /**
     * @var integer
     */
    private $height;

    /**
     * @var integer
     */
    private $numberY;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set description
     *
     * @param string $description
     * @return Map
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     * @return Map
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string 
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Map
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set numberX
     *
     * @param integer $numberX
     * @return Map
     */
    public function setNumberX($numberX)
    {
        $this->numberX = $numberX;

        return $this;
    }

    /**
     * Get numberX
     *
     * @return integer 
     */
    public function getNumberX()
    {
        return $this->numberX;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Map
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set numberY
     *
     * @param integer $numberY
     * @return Map
     */
    public function setNumberY($numberY)
    {
        $this->numberY = $numberY;

        return $this;
    }

    /**
     * Get numberY
     *
     * @return integer 
     */
    public function getNumberY()
    {
        return $this->numberY;
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
}
