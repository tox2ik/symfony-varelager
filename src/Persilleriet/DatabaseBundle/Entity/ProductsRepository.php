<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Mapping as ORM;

class ProductsRepository extends EntityRepository {

	public function findOneById($id) {
		try {
			/*
			$query = $this->getEntityManager()
				->createQuery('
				SELECT pc FROM DatabaseBundle:ProductCategories pc
				JOIN pc.supplier supp
				WHERE supp.id = :id 
				AND pc.name = :catname' 
			)->setParameter('id', $suppid)
			->setParameter('catname', $catname);
			*/
			$query = $this->getEntitymanager()->createQuery('
				SELECT	p, f
				FROM	DatabaseBundle:Products p
				LEFT 
				JOIN	p.filters f
				WHERE	p.id = :id
			')->setParameter('id', $id);
		} catch (\Exception $e) {
			error_log( "EX: findOneByIdFullyJoined(): " . $e->getMessage() );
		}

		try {
			return $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
}

?>
