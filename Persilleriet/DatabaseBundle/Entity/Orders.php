<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persilleriet\DatabaseBundle\Entity\Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Orders {

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
	 * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
	 * @var string $comment
     *
	 * @ORM\Column(name="comment", type="string", length=350, nullable=true)
     */
	private $comment;

	 /**
	  * @var Products
	  *
	  * @ORM\ManyToMany(targetEntity="Products", inversedBy="orders")
	  * @ORM\JoinTable(name="orders_data",
	  *		joinColumns={ @ORM\JoinColumn(name="order_id", referencedColumnName="id") },
	  *		inverseJoinColumns={ @ORM\JoinColumn(name="product_id", referencedColumnName="id") }
	  *		)
	  */
	 private $products;


    
	public function getComment() { return $this->comment; }
	public function setComment($comment) { $this->comment = $comment; }
	public function addProducts(\Persilleriet\DatabaseBundle\Entity\Products $product) { 
		$product->addOrder( $this );
		$this->product[] = $product; 
	}
    public function getDate() { return $this->date; }
    public function getId() { return $this->id; }
    public function getProducts() { return $this->products; }
    public function setDate($date) { $this->date = $date; }

}
