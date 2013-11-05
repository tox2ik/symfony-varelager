<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Mapping as ORM;

class SuppliersRepository extends EntityRepository {

	public function findOneBy(array $criteria) {
		$one = parent::findOneBy($criteria);
		//error_log( "user-repo fb1: ". print_r( $one ,true));
		return $one;
	}


	public function findOneByIdFullyJoined($id) {
		error_log("SUPP-ID: ". $id);
		try {
			$query = $this->getEntityManager()
				->createQuery('
				SELECT supplier,account,address,categories
				FROM DatabaseBundle:Suppliers supplier 
				JOIN supplier.account account 
				JOIN supplier.address address
				LEFT JOIN supplier.categories categories
				WHERE supplier.id = :id'
			)->setParameter('id', $id);
		} catch (\Exception $e) {
			error_log( "EX: findOneByIdFullyJoined(): " . $e->getMessage() );
		}

		try {
			return $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
/* put this in view_suppliers
	public function getSuppliersWithCategoriesList() {
		$result = null;
		try {
			$db_connection = $this->getEntityManager->getInstance()->getCurrentConnection();
			$mysql = "select
				suppliers.id as suppid, 
				suppliers.name as supplier,
				group_concat( product_categories.id separator \",\") as catids,
				group_concat( product_categories.name separator \",\") as catnames 
				from product_categories
				Right Join suppliers
				on product_categories.supplier_id = suppliers.id
				group by suppliers.id";
			$result = $q->execute($mysql);
		} catch (\Exception $e) {
			error_log("EX: getSuppliersWithAllCategories(): " . $e->getMessage());
			throw $e;
		}
		return $result
	}
*/
/*
		try {
			$db_connection = $this->getEntityManager->getInstance()->getCurrentConnection();
			$result = $q->execute($mysql);
		} catch (\Exception $e) {
			error_log("EX: getSuppliersWithAllCategories(): " . $e->getMessage());
			throw $e;
		}
		return $result
*/

}

?>
