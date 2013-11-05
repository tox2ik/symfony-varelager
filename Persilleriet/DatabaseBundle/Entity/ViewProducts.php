<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="view_products")
 * @ORM\Entity
 */
class ViewProducts {


	/**
	 * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
	 */
	private $id;

	/**
	 * @ORM\Column(name="partnum", type="string", length=55, nullable=false)
	 */
	private $partnum;

	/**
	 * @ORM\Column(name="product", type="string", length=100, nullable=false)
	 */
	private $product;


	/**
	 * @orm\column(name="price", type="decimal", nullable=false)
	 */
	private $price;


	/**
	 * @var boolean $expired
	 *
	 * @ORM\Column(name="expired", type="boolean", nullable=true)
	 */
	private $expired;


	/**
	 * @ORM\Column(name="category", type="string", length=20, nullable=false)
	 */
	private $category;

	/**
	 * @ORM\Column(name="supplier", type="string", length=55, nullable=false)
	 */
	private $supplier;



	public function getCategory()			{ return $this->category;}
	public function getExpired()			{ return $this->expired;}
	public function getId()					{ return $this->id;}
	public function getPartnum()			{ return $this->partnum;}
	public function getPrice()				{ return $this->price;}
	public function getProduct()			{ return $this->product;}
	public function getSupplier()			{ return $this->supplier;}
	public function setCategory($category) 	{ $this->category = $category;}
	public function setExpired($expired) 	{ $this->expired = $expired;}
	public function setId($id) 				{ $this->id = $id;}
	public function setPartnum($partnum) 	{ $this->partnum = $partnum;}
	public function setPrice($price) 		{ $this->price = $price;}
	public function setProduct($product) 	{ $this->product = $product;}
	public function setSupplier($supplier) 	{ $this->supplier = $supplier;}

}
