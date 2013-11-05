<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Persilleriet\DatabaseBundle\Entity\Addresses;
use \Persilleriet\DatabaseBundle\Entity\Accounts;

/**
 * Persilleriet\DatabaseBundle\Entity\Suppliers
 *
 * @ORM\Table(name="suppliers")
 * @ORM\Entity(repositoryClass="Persilleriet\DatabaseBundle\Entity\SuppliersRepository")
 */
class Suppliers
{

	public function __construct() {
		$this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
     * @ORM\Column(name="name", type="string", length=55, nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Accounts", inversedBy="supplier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     * })
     */
    private $account;

    /**
     * @ORM\OneToOne(targetEntity="Addresses", inversedBy="supplier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;


	/**
	 * @ORM\OneToMany(targetEntity="ProductCategories", mappedBy="supplier" )
	 */
	private $categories;

	public function removeCategoryWithName($title) {
		error_log('MOO  3');
		for ($i=$this->categories->count() -1; $i >= 0;$i--) {
			$cat = $this->categories->get($i);
			if ($cat->getName() == $title) {
				$this->categories->remove($i);
				return $cat;
				//$i = -1;
			}
		}
		error_log('MOO  5');
		return null;
	}
	public function addUniqueCategory($title) {
		$cats = array();
			error_log('MOO  2');
		foreach ($this->categories as $k => $cat) {
			$cats[]= $cat->getName();
		}
		if (!in_array($title, $cats)) {
			$cat = new ProductCategories();
			$cat->setName($title);
			$cat->setSupplier($this);
			$this->categories->add($cat);
		}
	}

	public function getCategories() { return $this->categories; }
	public function addCategory($c) { $this->categories->add($c); }
	public function hasCategory($c) {
		return $this->categories->contains($c);
	}

    public function getAccount() { return $this->account; }
    public function getAddress() { return $this->address; }
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function setAccount(Accounts $account) { 
		$account->setSupplier($this);
		$this->account = $account; 
	}
    public function setAddress(Addresses $address) { 
		$address->setSupplier($this);
		$this->address = $address; 
}
    public function setName($name) { $this->name = $name; }
}
