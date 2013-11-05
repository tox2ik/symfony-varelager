<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\Departments
 *
 * @ORM\Table(name="departments")
 * @ORM\Entity
 */
class Departments {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=55, nullable=false)
     */
    protected $name;

    /**
     * @var boolean $stockroom
     *
     * @ORM\Column(name="stockroom", type="boolean", nullable=false)
     */
    protected $stockroom;

    /**
     * @var Addresses
     *
     * @ORM\ManyToOne(targetEntity="Addresses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    protected $address;



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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set stockroom
     *
     * @param boolean $stockroom
     */
    public function setStockroom($stockroom)
    {
        $this->stockroom = $stockroom;
    }

    /**
     * Get stockroom
     *
     * @return boolean 
     */
    public function getStockroom()
    {
        return $this->stockroom;
    }

    /**
     * Set address
     *
     * @param Persilleriet\DatabaseBundle\Entity\Addresses $address
     */
    public function setAddress(\Persilleriet\DatabaseBundle\Entity\Addresses $address)
    {
        $this->address = $address;
    }

    /**
     * Get address
     *
     * @return Persilleriet\DatabaseBundle\Entity\Addresses 
     */
    public function getAddress()
    {
        return $this->address;
    }
}
