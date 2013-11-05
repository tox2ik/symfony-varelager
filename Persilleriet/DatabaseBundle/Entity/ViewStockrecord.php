<?php
namespace Persilleriet\DatabaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 *
 * @ORM\Table(name="view_stockrecord")
 * @ORM\Entity
 */
class ViewStockrecord {

/**
 * @ORM\Id
 * @ORM\Column(name="uid", type="integer", nullable=false)
 */
private $uid;

/**
 * @ORM\Id
 * @ORM\Column(name="srid", type="integer", nullable=false)
 */
private $srid;

/**
 * @ORM\Id
 * @ORM\Column(name="prid", type="integer", nullable=false)
 */
private $prid;

/**
 * @ORM\Id
 * @ORM\Column(name="depid", type="integer", nullable=false)
 */
private $depid;

/**
 * @ORM\Column(name="qty", type="integer", nullable=false)
 */
private $qty;

/**
 * @ORM\Column(name="uname", type="string", length="25", nullable=false)
 */
private $uname;

/**
 * @ORM\Column(name="department", type="string", length="55", nullable=false)
 */
private $department;

/**
 * @ORM\Column(name="date")
 */
private $date;

/**
 * @ORM\Column(name="product", type="string", length="100", nullable=false)
 */
private $product;

/**
 * @ORM\Column(name="supplier", type="string", length="55", nullable=false)
 */
private $supplier;

/**
 * @ORM\Column(name="category", type="string", length="20", nullable=false)
 */
private $category;

/**
 * @ORM\Column(name="partnum", type="string", length="55", nullable=false)
 */
private $partnum;

public function getDate()			{ return $this->date;}
public function getDepartment()		{ return $this->department;}
public function getDepid()			{ return $this->depid;}
public function getPrid()			{ return $this->prid;}
public function getProduct()		{ return $this->product;}
public function getQty()			{ return $this->qty;}
public function getSrid()			{ return $this->srid;}
public function getUid()			{ return $this->uid;}
public function getUname()			{ return $this->uname;}
public function getPartnum()		{ return $this->partnum;}
public function getSupplier()		{ return $this->supplier;}
public function getCategory()		{ return $this->category;}
public function setDate($date) 				{$this->date = $date;}
public function setDepartment($department) 	{$this->department = $department;}
public function setDepid($depid) 			{$this->depid = $depid;}
public function setPrid($prid) 				{$this->prid = $prid;}
public function setProduct($product) 		{$this->product = $product;}
public function setQty($qty) 				{$this->qty = $qty;}
public function setSrid($srid) 				{$this->srid = $srid;}
public function setUid($uid) 				{$this->uid = $uid;}
public function setUname($uname) 			{$this->uname = $uname;}
public function setPartnum($partnum)		{$this->partnum = $partnum;}
public function setSupplier($supplier)		{$this->supplier = $supplier;}
public function setCategory($category)		{$this->category = $category;}
}
?>
