<?php

namespace IamRainforestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Square
 */
class Square
{
    /**
     * @var integer
     */
    private $coordX;

    /**
     * @var integer
     */
    private $coordY;

    /**
     * @var \DateTime
     */
    private $boughtAt;

    /**
     * @var string
     */
    private $personalText;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \IamRainforestBundle\Entity\User
     */
    private $user;

    /**
     * @var \IamRainforestBundle\Entity\Map
     */
    private $map;


    /**
     * Set coordX
     *
     * @param integer $coordX
     * @return Square
     */
    public function setCoordX($coordX)
    {
        $this->coordX = $coordX;

        return $this;
    }

    /**
     * Get coordX
     *
     * @return integer 
     */
    public function getCoordX()
    {
        return $this->coordX;
    }

    /**
     * Set coordY
     *
     * @param integer $coordY
     * @return Square
     */
    public function setCoordY($coordY)
    {
        $this->coordY = $coordY;

        return $this;
    }

    /**
     * Get coordY
     *
     * @return integer 
     */
    public function getCoordY()
    {
        return $this->coordY;
    }

    /**
     * Set boughtAt
     *
     * @param \DateTime $boughtAt
     * @return Square
     */
    public function setBoughtAt($boughtAt)
    {
        $this->boughtAt = $boughtAt;

        return $this;
    }

    /**
     * Get boughtAt
     *
     * @return \DateTime 
     */
    public function getBoughtAt()
    {
        return $this->boughtAt;
    }

    /**
     * Set personalText
     *
     * @param string $personalText
     * @return Square
     */
    public function setPersonalText($personalText)
    {
        $this->personalText = $personalText;

        return $this;
    }

    /**
     * Get personalText
     *
     * @return string 
     */
    public function getPersonalText()
    {
        return $this->personalText;
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
     * Set user
     *
     * @param \IamRainforestBundle\Entity\User $user
     * @return Square
     */
    public function setUser(\IamRainforestBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \IamRainforestBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set map
     *
     * @param \IamRainforestBundle\Entity\Map $map
     * @return Square
     */
    public function setMap(\IamRainforestBundle\Entity\Map $map = null)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map
     *
     * @return \IamRainforestBundle\Entity\Map 
     */
    public function getMap()
    {
        return $this->map;
    }
}
