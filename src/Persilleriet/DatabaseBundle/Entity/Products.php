<?php

namespace Persilleriet\DatabaseBundle\Entity;
use Persilleriet\DatabaseBundle\Entity\ProductFilter;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\Products
 *
 * @ORM\Table(name="products")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Persilleriet\DatabaseBundle\Entity\ProductsRepository")
 */
class Products {


    public function __construct() {

		$this->filters	= new \Doctrine\Common\Collections\ArrayCollection();
        $this->order 	= new \Doctrine\Common\Collections\ArrayCollection();
    	$this->stockrecord = new \Doctrine\Common\Collections\ArrayCollection();
    }
	public function __toString() {
		return sprintf("id:%s, expired:%b, name:%s,  partnum:%s, price:%s", 
			$this->id,
			$this->expired,
			$this->name,
			$this->partnum ==null ? 'null' : $this->partnum,
			$this->price
		);
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string $partnum
     * @ORM\Column(name="partnum", type="string", length=55, nullable=false)
     */
    private $partnum;

    /**
     * @var decimal $price
     * @ORM\Column(name="price", type="decimal", nullable=false)
     */
    private $price;

    /**
     * @var boolean $expired
     * @ORM\Column(name="expired", type="boolean", nullable=true)
     */
    private $expired;

    /**
     * @var Orders
     * @ORM\ManyToMany(targetEntity="Orders", mappedBy="products")
     */
    private $orders;

    /**
     * @var ProductCategories
     * @ORM\ManyToOne(targetEntity="ProductCategories", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;


	/**
	 * @ORM\ManyToMany(targetEntity="ProductFilter", mappedBy="products")
	 */
	private $filters;

	public function addFilter(ProductFilter $f) {
		$this->filters->add($f);
	}

	public function removeFilter(ProductFilter $f) {
		$this->filters->removeElement($f);
	}
	public function removeFilterByKey($f) {
		$this->filters->remove($f);
	}


    public function addOrder(\Persilleriet\DatabaseBundle\Entity\Orders $order) { $this->order[] = $order; }
    public function addStockrecord(\Persilleriet\DatabaseBundle\Entity\Stockrecord $stockrecord) { $this->stockrecord[] = $stockrecord; }
    public function getCategory() { return $this->category; }
    public function getExpired() { return $this->expired; }
    public function getFilters() { return $this->filters; }
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getOrder() { return $this->orders; }
    public function getPartnum() { return $this->partnum; }
    public function getPrice() { return $this->price; }
    public function getStockrecord() { return $this->stockrecord; }
    public function setCategory(\Persilleriet\DatabaseBundle\Entity\ProductCategories $category) { $this->category = $category; }
    public function setExpired($expired) { $this->expired = $expired; }
    public function setName($name) { $this->name = $name; }
    public function setPartnum($partnum) { $this->partnum = $partnum; }
    public function setPrice($price) { $this->price = $price; }
}
