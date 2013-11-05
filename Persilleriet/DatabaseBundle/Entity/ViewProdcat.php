<?php
namespace Persilleriet\DatabaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 *
 * @ORM\Entity
 * @ORM\Table(name="view_prodcat")
 */
class ViewProdcat {

		/**
		 * @ORM\Id
		 * @ORM\Column(name="prodcat_id", type="integer", nullable=false)
		 */
		private $prodcatId;

		/**
		 * @ORM\Column(name="category", type="string", length="20", nullable=false)
		 */
		private $category;

		/**
		 * @ORM\Column(name="supp_id", type="integer", nullable=false)
		 */
		private $suppId;

		/**
		 * @ORM\Column(name="supplier", type="string", length="55", nullable=false)
		 */
		private $supplier;
		public function getCategory()			{ return $this->category;}
		public function getProdcatId()			{ return $this->prodcatId;}
		public function getSuppId()			{ return $this->suppId;}
		public function getSupplier()			{ return $this->supplier;}
		public function setCategory($category) 	{$this->category = $category;}
		public function setProdcatId($prodcatId) 	{$this->prodcatId = $prodcatId;}
		public function setSuppId($suppId) 	{$this->suppId = $suppId;}
		public function setSupplier($supplier) 	{$this->supplier = $supplier;}
}
