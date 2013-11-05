<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\Accounts
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity
 */
class Accounts
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=25, nullable=false)
     */
    private $name;

    /**
     * @var string $number
     *
     * @ORM\Column(name="number", type="string", length=30, nullable=true)
     */
    private $number;

    /**
     * @var string $customerIdentification
     *
     * @ORM\Column(name="customer_identification", type="string", length=55, nullable=true)
     */
    private $customerIdentification;

	/**
	 * @ORM\OneToOne(targetEntity="Suppliers", mappedBy="account")
	 */
	private $supplier;

    public function getCustomerIdentification() { return $this->customerIdentification; }
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getNumber() { return $this->number; }
	public function getSupplier() { return $this->supplier; }
    public function setCustomerIdentification($customerIdentification) { 
		$this->customerIdentification = $customerIdentification; }
    public function setName($name) { $this->name = $name; }
    public function setNumber($number) { $this->number = $number; }
    public function setSupplier($supplier) { $this->supplier = $supplier; }
	
}
