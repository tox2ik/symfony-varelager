<?php
namespace Persilleriet\DatabaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 *
 * @ORM\Entity
 * @ORM\Table(name="view_supplier")
 */
class ViewSuppliers {

/**
 * @ORM\Id
 * @ORM\Column(name="id", type="integer", nullable=false)
 */
private $id;

/**
 * @ORM\Column(name="name", type="string", length="55", nullable=false)
 */
private $name;

/**
 * @ORM\Column(name="account_id", type="integer", nullable=false)
 */
private $account_id;

/**
 * @ORM\Column(name="account_number", type="string", length="30", nullable=false)
 */
private $account_number;

/**
 * @ORM\Column(name="default_cid", type="string", length="55", nullable=false)
 */
private $default_cid;

/**
 * @ORM\Column(name="address_id", type="integer", nullable=false)
 */
private $address_id;

/**
 * @ORM\Column(name="street", type="string", length="100", nullable=false)
 */
private $street;

/**
 * @ORM\Column(name="city", type="string", length="80", nullable=false)
 */
private $city;

/**
 * @ORM\Column(name="zipcode", type="integer", nullable=false)
 */
private $zipcode;

/**
 * @ORM\Column(name="country", type="string", length="40", nullable=false)
 */
private $country;

/**
 * @ORM\Column(name="phone", type="string", length="17", nullable=false)
 */
private $phone;

/**
 * @ORM\Column(name="email", type="string", length="55", nullable=false)
 */
private $email;

/**
 * @ORM\Column(name="addr_comment", type="string", length="700", nullable=false)
 */
private $addr_comment;

/**
 * @ORM\Column(name="catids", type="string", length="2000" )
 */
// comma-separated list of product categories (row ID) associated with this supplier 
private $catids;

/**
 * @ORM\Column(name="catnames", type="string", length="2000" )
 */
// comma-separated list of product categories (column NAME) associated with this supplier 
private $catnames;

public function getAccId()			{ return $this->accId;}
public function getAccNumber()			{ return $this->accNumber;}
public function getAddrComment()			{ return $this->addrComment;}
public function getAddrId()			{ return $this->addrId;}
public function getCatIds() 		{ return $this->catids;}
public function getCatNames() 		{ return $this->catnames;}
public function getCity()			{ return $this->city;}
public function getCountry()			{ return $this->country;}
public function getDefCid()			{ return $this->defCid;}
public function getEmail()			{ return $this->email;}
public function getId()			{ return $this->id;}
public function getName()			{ return $this->name;}
public function getPhone()			{ return $this->phone;}
public function getStreet()			{ return $this->street;}
public function getZipcode()			{ return $this->zipcode;}
public function setAccId($accId) 	{$this->accId = $accId;}
public function setAccNumber($accNumber) 	{$this->accNumber = $accNumber;}
public function setAddrComment($addrComment) 	{$this->addrComment = $addrComment;}
public function setAddrId($addrId) 	{$this->addrId = $addrId;}
public function setCatIds($catids) 	{$this->catids = $catids;}
public function setCatNames($catnames) 	{$this->catnames = $catnames;}
public function setCity($city) 	{$this->city = $city;}
public function setCountry($country) 	{$this->country = $country;}
public function setDefCid($defCid) 	{$this->defCid = $defCid;}
public function setEmail($email) 	{$this->email = $email;}
public function setId($id) 	{$this->id = $id;}
public function setName($name) 	{$this->name = $name;}
public function setPhone($phone) 	{$this->phone = $phone;}
public function setStreet($street) 	{$this->street = $street;}
public function setZipcode($zipcode) 	{$this->zipcode = $zipcode;}

}
