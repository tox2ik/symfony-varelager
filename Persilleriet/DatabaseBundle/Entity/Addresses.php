<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\Addresses
 *
 * @ORM\Table(name="addresses")
 * @ORM\Entity
 */
class Addresses
{
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
     * @var string $street
     *
     * @ORM\Column(name="street", type="string", length=100, nullable=false)
     */
    protected $street;

    /**
     * @var integer $zipcode
     *
     * @ORM\Column(name="zipcode", type="integer", nullable=false)
     */
    protected $zipcode;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=80, nullable=false)
     */
    protected $city;

    /**
     * @var string $country
     *
     * @ORM\Column(name="country", type="string", length=40, nullable=false)
     */
    protected $country;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=17, nullable=false)
     */
    protected $phone;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=55, nullable=false)
     */
    protected $email;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="string", length=700, nullable=true)
     */
    protected $comment;

	/**
	 * @ORM\OneToOne(targetEntity="User", mappedBy="address")
	 */
	protected $user;
	
	/**
	 * @ORM\OneToOne(targetEntity="Suppliers", mappedBy="address")
	 */
	protected $supplier;

	public function getSupplier() { return $this->supplier; }
	public function getUser() { return $this->user; }
	public function setUser(User $user) {$this->user = $user; }
    public function getCity() { return $this->city; }
    public function getComment() { return $this->comment; }
    public function getCountry() { return $this->country; }
    public function getEmail() { return $this->email; }
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getPhone() { return $this->phone; }
    public function getStreet() { return $this->street; }
    public function getZipcode() { return $this->zipcode; }
    public function setCity($city) { $this->city = $city; }
    public function setComment($comment) { $this->comment = $comment; }
    public function setCountry($country) { $this->country = $country; }
    public function setEmail($email) { $this->email = $email; }
    public function setName($name) { $this->name = $name; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setStreet($street) { $this->street = $street; }
    public function setSupplier($supplier) { $this->supplier = $supplier; }
    public function setZipcode($zipcode) { $this->zipcode = $zipcode; }
}
