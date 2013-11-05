<?php

namespace Persilleriet\DatabaseBundle\Entity;

use \Doctrine\Common\Collections\ArrayCollection;
use \Persilleriet\DatabaseBundle\Entity\StockrecordData;
use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\Stockrecord
 *
 * @ORM\Table(name="stockrecord")
 * @ORM\Entity
 */
class Stockrecord {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var date $date
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

//    /**
//	 * @var StockrecordData
//	 * @ORM\OneToMany(targetEntity="StockrecordData", mappedBy="stockrecord")
//     *
//     */
//	private $stockrecordData;

    /**
     * @var Departments
     *
     * @ORM\ManyToOne(targetEntity="Departments")
	 *
	 *
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private $employee;

    public function __construct() {
		//$this->stockrecordData = new ArrayCollection();
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
     * Set date
     *
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return date 
     */
    public function getDate()
    {
        return $this->date;
    }

//    /**
//	 * Add stockrecorddata
//     *
//	 * @param Persilleriet\DatabaseBundle\Entity\StockrecordData $sr_data
//     */
//	public function addStockrecord(StockrecordData $sr_data) {
//		$this->stockrecordData[] = $sr_data;
//    }

    /**
     * Get product
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set department
     *
     * @param Persilleriet\DatabaseBundle\Entity\Departments $department
     */
    public function setDepartment(\Persilleriet\DatabaseBundle\Entity\Departments $department)
    {
        $this->department = $department;
    }

    /**
     * Get department
     *
     * @return Persilleriet\DatabaseBundle\Entity\Departments 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set employee
     *
     * @param Persilleriet\DatabaseBundle\Entity\User $employee
     */
    public function setUser(\Persilleriet\DatabaseBundle\Entity\User $employee)
    {
        $this->employee = $employee;
    }

    /**
     * Get employee
     *
     * @return Persilleriet\DatabaseBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->employee;
    }

	public function __toString() {
			return "Stockrecord{ id: {$this->id}, date: {$this->date}, employee: {$this->employee}}";
	}
}
