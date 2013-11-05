<?php

namespace Persilleriet\DatabaseBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;

use Doctrine\ORM\Mapping as ORM;

class UserRepository extends EntityRepository implements UserProviderInterface { 

	public function loadUserByUsername($username) {
		$q = $this 
			->createQueryBuilder('u')
			->select('u, a, r')
			->leftJoin('u.address', 'a')
			->leftJoin('u.roles', 'r', Join::WITH, 'r.virtual_role = 1')
		                                                //$qb->expr()->eq('g.manager_level', '100')
			->where('u.username = :username')
			->orWhere('a.email = :email')
			->setParameter('username', $username)
			->setParameter('email', $username)
			->getQuery() ;

		/*
		$q = $this->getEntityManager()
			->createQuery(
			'SELECT u, a FROM DatabaseBundle:User u LEFT JOIN u.address a '.
			'WHERE u.username = :user OR a.email = :email'
		)
		->setParameter('user', $username)
		->setParameter('email', $username)
		;
		*/


		try {
			// The Query::getSingleResult() method throws an exception
			// if there is no record matching the criteria.

			$user = $q->getSingleResult();

		} catch (NoResultException $e) {
			throw new UsernameNotFoundException(
				sprintf('Unable to find an active user' .
				'PersillerietDatabaseBundle:User object identified by "%s".', $username), 
				null, 0, $e);
		}

		return $user;
	}

	public function refreshUser(UserInterface $user) {
		$class = get_class($user);
		if (!$this->supportsClass($class)) {
			throw new UnsupportedUserException(
				sprintf('Instances of "%s" are not supported.', $class));
		}

		return $this->loadUserByUsername($user->getUsername());
	}

	public function supportsClass($class) {
		return 
			$this->getEntityName() === $class || 
			is_subclass_of($class, $this->getEntityName());
	}
	public function findOneBy(array $criteria) {
		$one = parent::findOneBy($criteria);
		//error_log( "user-repo fb1: ". print_r( $one ,true));
		return $one;
	}

	public function findOneByIdJoinedToAddress($id) {
		$query = $this->getEntityManager()
			->createQuery('
			SELECT user, addr FROM DatabaseBundle:User user
			JOIN user.address addr
			WHERE user.id = :id'
		)->setParameter('id', $id);

		try {
			return $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
}

?>
