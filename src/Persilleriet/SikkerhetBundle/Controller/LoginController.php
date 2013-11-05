<?php

namespace Persilleriet\SikkerhetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class LoginController extends Controller {
    /**
	 * @Route("/login", name="_login")
     * @Template()
     */
    public function loginAction() {
		if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $this->get('request')->attributes->get(
				SecurityContext::AUTHENTICATION_ERROR);
        } else {
			$error = $this->get('request')->getSession()-> get(
				SecurityContext::AUTHENTICATION_ERROR);
        }

		//return $this->render('SikkerhetBundle:Login:login.html.twig', array(
		return array(
			'last_username' => $this->get('request')->getSession()->get(
										SecurityContext::LAST_USERNAME),
            'error'         => $error,
		//));
		);
    }

    /**
	 * @Route("/login_check", name="_login_check")
     */
	public function securityCheckAction() { 
		/* The security layer will intercept this request */ 
		return; 
	}

    /**
	 * @Route("/logout", name="_logout")
     */
	public function logoutAction() { 
		/* The security layer will intercept this request */ 
		return;
	}

//    /**
//     * @Route("/hello", defaults={"name"="World"}),
//     * @Route("/hello/{name}", name="_demo_secured_hello")
//     * @Template()
//     */
//    public function helloAction($name)
//    {
//        return array('name' => $name);
//    }
//
//    /**
//     * @Route("/hello/admin/{name}", name="_demo_secured_hello_admin")
//     * @Secure(roles="ROLE_ADMIN")
//     * @Template()
//     */
//    public function helloadminAction($name)
//    {
//        return array('name' => $name);
//    }
}
