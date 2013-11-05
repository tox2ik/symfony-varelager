<?php
namespace Persilleriet\DatabaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Persilleriet\DatabaseBundle\Entity\Orders;
use Persilleriet\DatabaseBundle\Entity\Products;
/**
 *
 * @ORM\Table(name="orders_data")
 * @ORM\Entity
 */
class OrdersData {

	public function __construct() {
	}

	/**
	 * @var Orders
	 *
     * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Orders")
     * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * })
	 */
	private $orders;

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
		return $this->product;
	}
	public function getOrders() { 
		return $this->orders;
	}
	public function getQuantity() { 
		return $this->quantity;
	}
	public function setOrder(Orders $order) {
			$this->orders = $order;
	}
	public function setProduct(Products $product) {
		$this->product = $product;
	}
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
	}

	public function __toString() {
		$id = $this->orders->getId();
		$prid = $this->product->getId();
		return "OrdersData{ order: $id, product: $prid, qty: {$this->quantity}}";
	}
}
?>
