<?php

namespace Persilleriet\VarelagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller {
    public function indexAction() {
		//return $this->render('VarelagerBundle:Welcome:index.html.twig');
		return $this->redirect($this->generateUrl('_varelager'));
    }
}
