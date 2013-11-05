<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\ProductCategories
 *
 * @ORM\Table(name="product_categories")
 * @ORM\Entity(repositoryClass="Persilleriet\DatabaseBundle\Entity\ProductCategoriesRepository")
 */
class ProductCategories
{

	public function __toString() {
		return "PRODCATS $this->id";
	}
	public function __construct() {
		$this->products = new \Doctrine\Common\Collections\ArrayCollection();
	}
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
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;


    ///**
    // * @ORM\Column(name="name", type="integer", nullable=false)
    // */
    //private $supplier_id;

    /**
	 * @var Suppliers
	 * @ORM\ManyToOne(targetEntity="Suppliers", inversedBy="categories")
	 * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
	private $supplier;

	/*
	 * @ORM\OneToMany(targetEntity="Products", mappedBy="category")
	 */
	private $products;

    public function getId() { return $this->id; }
    public function setName($name) { $this->name = $name; }
    public function getName() { return $this->name; }
	//public function getSupplierId($supplierId) { $this->supplierId = $supplierId; }
	public function setSupplier(Suppliers $supplier) { $this->supplier = $supplier; }
	public function getSupplierId() { return $this->supplier->getId(); } 
	//public function getSupplier_id() {return $this->supplierId; }
	//public function setSupplier_id($id) { $this->supplierId = $id; }
}
