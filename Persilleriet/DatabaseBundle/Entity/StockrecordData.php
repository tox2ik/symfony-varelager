<?php
namespace Persilleriet\DatabaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Persilleriet\DatabaseBundle\Entity\Stockrecord;
use Persilleriet\DatabaseBundle\Entity\Products;
/**
 *
 * @ORM\Table(name="stockrecord_data")
 * @ORM\Entity
 */
class StockrecordData {

	public function __construct() {
		//$this->stockrecord = new \Doctrine\Common\Collections\ArrayCollection();
		//$this->product = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @var Stockrecord 
	 *
     * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Stockrecord")
     * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="stockrecord_id", referencedColumnName="id")
     * })
	 */
	private $stockrecord;

	/**
     * @var Products
	 *
     * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Products")
     * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     *
     */
	private $product;

	/**
	 * @ORM\Column(name="quantity", type="integer", nullable=false)
	 */
	private $quantity;

	public function getProduct()			{ 
		//return $this->product->get(0);
		return $this->product;
	}
	public function getStockrecord() { 
		//return $this->stockrecord->get(0);
		return $this->stockrecord;
	}
	public function getQuantity() { 
		return $this->quantity;
	}
	public function setStockrecord(Stockrecord $stockrecord) {
		//$this->stockrecord->set(0, $stockrecord);
		$this->stockrecord = $stockrecord;
	}
	public function setProduct(Products $product) {
		//$this->product->set(0, $product);
		$this->product = $product;
	}
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
	}

	public function __toString() {
		$srid = $this->stockrecord->getId();
		$prid = $this->product->getId();
		return "StockrecordData{ stockrecord: $srid, product: $prid, qty: {$this->quantity}}";
	}

}
?>
