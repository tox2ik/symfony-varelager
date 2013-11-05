<?php

namespace Persilleriet\DatabaseBundle\Entity;
use Persilleriet\DatabaseBundle\Entity\Products;
use Persilleriet\DatabaseBundle\Entity\Departments;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="product_filter")
 * @ORM\Entity
 */
class ProductFilter {

	public function __construct() {
		$this->products = new \Doctrine\Common\Collections\ArrayCollection();
	}
	public function __toString() {
		return sprintf("id:%s, name:%s, prods:%d", $this->id, $this->name, $this->products->count() );
	}

	/**
	 * @ORM\Column(name="id", type="integer", nullable="FALSE")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\Id
	 */
	private $id;

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
	 * @ORM\Column(name="name", type="string", length="55", nullable="FALSE")
	 */
	private $name;

	/**
	 * @ORM\Column(name="comment", type="string", length="350", nullable="FALSE")
	 */
	private $comment;

	/** 
	 * @ORM\Column(name="created", type="datetime", nullable=false) 
	 */
	private $created;

	/**
	 * @ORM\ManyToMany(targetEntity="Products", inversedBy="filters")
	 * @ORM\JoinTable(name="product_filter_data",
	 *		joinColumns={@ORM\JoinColumn(name="product_filter_id", referencedColumnName="id")},
	 *		inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")}
	 * )
	 */
	private $products;

	//public function addProduct($prod) {
	//	$prod->addFilter($this);
	//	$this->products[] = $prod;
	//}


	public function addProduct(Products $prod) {
		$prod->addFilter($this);
		$this->products->add($prod);
	}
	public function removeProduct(Products $prod) {
		$prod->removeFilter($this);
		$this->products->removeElement($prod);
	}
	public function removeProductByKey($key) {
		$p = $this->products->remove($key);
		if ($p !=null) {
			$p->removeFilter($this);
		} else {
			throw new \Exception('no such key');
		}

	}
	public function getComment()			{ return $this->comment;}
	public function getDepartment()			{ return $this->department;}
	public function getId()					{ return $this->id;}
	public function getName()				{ return $this->name;}
	public function getDate()				{ return $this->created;}
	public function getCreated()			{ return $this->created;}
	public function setComment($comment) 	{$this->comment = $comment;}
	public function setDepartment(Departments $department)
											{$this->department = $department; }
	public function setId($id) 				{$this->id = $id;}
	public function setName($name)		 	{$this->name = $name;}
	public function setDate(\DateTime $d) 	{$this->created = $d;}
	public function setCreated(\DateTime $d){$this->created = $d;}
}
?>
