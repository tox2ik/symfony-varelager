<?php

namespace Persilleriet\VarelagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MooController extends Controller {
	public function indexAction(Request $r) {
		//return new Response('moo', 200, array('Content-Type' => 'text/plain'));
		return $this->indexAction5($r);
	}

	public function resetPassAction(Request $r) {
		return $this->indexAction4($r);
	}

	public function indexAction5(Request $request) {
		$ret = '';

		$ret .= print_r($request->query->get('hei', 'haei'), true);


		$input  = 'tox2ik';
		$input  = 'jazzoslav@gmail.com';
		$em = $this->getDoctrine()->getEntityManager();
		$q = $em->createQueryBuilder()
			->select('u, a')
			->from('DatabaseBundle:User', 'u')
			->leftJoin('u.address', 'a')
			->where('u.username = :user OR a.email = :mail')
			->setParameter('user', $input)
			->setParameter('mail', $input)
			->getQuery();

		$rows = $q->getResult($q::HYDRATE_ARRAY);

		$rA = print_r($rows, true);
		$rA = str_replace("    ", " ", $rA);


		$ret .="\n-------\n";
		$ret .=$rA;


		return new Response($ret, 200, array('Content-Type' => 'text/plain'));
	}

	public function indexAction4() { // CHANGE PASS

		$em = $this->getDoctrine()->getEntityManager();

		$user = $this->getDoctrine()
				->getRepository('DatabaseBundle:User')
				->find(1); // 1 - jarosalv
		$user->setUsername('tox2ik');

		$enc  = $this->get('security.encoder_factory')->getEncoder($user);
		$pass = $enc->encodePassword('uberhaxx0r', $user->getSalt() );
		$user->setPassword($pass);

		$em->persist($user);
	    $em->flush();
		return new Response('new pass set', 200, array('Content-Type' => 'text/plain'));

	}

	public function indexAction3() { // CREATE USER 

		$user = new \Persilleriet\DatabaseBundle\Entity\User();

		$enc  = $this->get('security.encoder_factory')->getEncoder($user);
		$pass = $enc->encodePassword('hei', $user->getSalt() );
		$user->setPassword($pass);

		$user->setNameFirst('Båb Kåre');
		$user->setNameLast('Ytrefoss');
		$user->setUsername('bob');
		$user->setAddressId(14);
		$user->setActive(1);

		$em = $this->getDoctrine()->getEntityManager();
		$em->persist($user);
	    $em->flush();

		//return new Response($print_r($user, true), 200, array('Content-Type' => 'text/plain'));
		return new Response('moo done', 200, array('Content-Type' => 'text/plain'));


	}
    public function indexAction2() {
		$moo = "troll\n";

		$factory = $this->get('security.encoder_factory');
		//$user = new Persilleriet\DatabaseBundle\Entity\Emplyes();

		$encoder = $factory->getEncoder($user);
		$password = $encoder->encodePassword('hei', $user->getSalt());

		$user = $this->getDoctrine()
				->getRepository('DatabaseBundle:User')
				->find(1); // 1 - jarosalv

		$user->setPassword($password);




		$em = $this->getDoctrine()->getEntityManager();
	    $em->persist($product);
	    $em->flush();


		return new Response($moo, 200, array('Content-Type' => 'text/plain'));
    }
}
