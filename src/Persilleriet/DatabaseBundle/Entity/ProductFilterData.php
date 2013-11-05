<?php
namespace Persilleriet\DatabaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Persilleriet\DatabaseBundle\Entity\Stockrecord;
use Persilleriet\DatabaseBundle\Entity\Products;
/**
 *
 * @ORM\Table(name="product_filter_data")
 * @ORM\Entity
 */
class ProductFilterData {

	public function __construct() {
	}

	/**
	 * @var ProductFilter
	 *
     * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="ProductFilter")
     * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="product_filter_id", referencedColumnName="id")
     * })
	 */
	private $productfilter;

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
	 * @ORM\Column(name="quantity", type="integer", nullable=true)
	 */
	private $quantity;

	public function getProduct()			{ 
		return $this->product;
	}
	public function getProductFilter() { 
		return $this->productfilter;
	}
	public function getQuantity() { 
		return $this->quantity;
	}
	public function setProductFilter(ProductFilter $productfilter) {
			$this->productfilter = $productfilter;
	}
	public function setProduct(Products $product) {
		$this->product = $product;
	}
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
	}

	public function __toString() {
		$id = $this->productfilter->getId();
		$prid = $this->product->getId();
		return "ProductFilterData{ pfid: $id, product: $prid, qty: {$this->quantity}}";
	}

}
?>
