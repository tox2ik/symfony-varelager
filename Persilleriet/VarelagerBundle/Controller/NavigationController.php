<?php

namespace Persilleriet\VarelagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Persilleriet\VarelagerBundle\Controller\VarelagerController;

class NavigationController extends Controller {
	public function generateNavigationListAction(Request $req) {
		$req_uri = $req->server->get('REQUEST_URI');
		$links = array();
		$routes = array(
			'_varelager' => 'Lagerbeholdning',
			'_varelager_bestilling' => 'Bestilling',
			'_varelager_historikk' => 'Historikk',
			'_varelager_adminpanel' => 'Administrasjon'
		);
		foreach ($routes as $r => $path) {
			$href ="";
			try {
				$router = $this->get('router');
				$href = $router->generate($r, array(), false); // false = not absolute

				$pattern = str_replace('/','\/', $req_uri);
				$selected = preg_match("/$pattern$/", $href);
			} catch (\Exception $e) {
				error_log( print_r( $e->getMessage() , true));
			}
			$links[] = array( 
				'route'=> $r, 
				'html' => $routes[$r],
				'selected' => $selected ? 'navigation selected' : 'navigation',
				'href' => $href
			);
		}
		return $this->render('VarelagerBundle:Varelager:navigation.html.twig', 
			array('links'=> $links ));
	}
}
