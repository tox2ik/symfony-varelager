<?php

namespace Persilleriet\VarelagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Persilleriet\VarelagerBundle\Form\ContactType;
use Persilleriet\DatabaseBundle\Controller\DatabaseController;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class VarelagerController extends Controller {

	private $aCreateOrder	= array( 'value' => 'createOrder',  	'legend' => 'Opprett bestilling');
	private $aSaveOrder		= array( 'value' => 'saveOrder',		'legend' => 'Lagre ordre');
	private $aSaveStock		= array( 'value' => 'saveStock',		'legend' => 'Registér varetelling');
	private $aSaveFilter	= array( 'value' => 'saveFilter',		'legend' => 'Lagre mal');
	private $aFetchFilter	= array( 'value' => 'fetchFilter',		'legend' => 'Hent mal');

	private $aFetchUsers	= array( 'value' => 'Users',		'legend' => 'Brukere');
	private $aFetchPerms	= array( 'value' => 'Permissions',	'legend' => 'Rettigheter');
	private $aFetchProds	= array( 'value' => 'Products',		'legend' => 'Produkter');
	private $aFetchDeps		= array( 'value' => 'Departments',	'legend' => 'Avdelinger');
	private $aFetchSuppliers= array( 'value' => 'Suppliers',	'legend' => 'Leverandører');
	private $aFetchOrders	= array( 'value' => 'Orders',		'legend' => 'Bestillinger');

	private $dNoDepartment	= array( 'id' => '0',	'name' => 'Ikke oppgitt');

		//array( 'value' => 'fetchList',			'legend' => 'Hent')
		//array( 'value' => 'accumulateLists',	'legend' => 'Akkumuler lister'),
		//array( 'value' => 'saveFilter',		'legend' => 'Lagre utvalg som filter')


    /**
	 * @Route("/varelager", name="_varelager")
     * @Template()
     */
    public function indexAction() {

		$departments = $this->getStorageDepartments(); // populates <select> utsalgssted
		$departments = array_merge(array( $this->dNoDepartment), $departments );

		$actions[]= $this->aFetchFilter;
		$actions[]= $this->aSaveFilter;
		$actions[]= $this->aSaveStock;
		$actions[]= $this->aCreateOrder;

		return array( 
			'departments' => $departments,
			'actionChoices' => $actions
		);

	//<option value="saveStock">Lagre beholdning</option>
	//<option value="saveFilter">Lagre utvalg som filter</option> 

    }

    /**
	 * @Route("/varelager/historikk", name="_varelager_historikk")
     */
	public function historikkAction() {

		$departments = $this->getStorageDepartments(); // populates <select> utsalgssted
		$actions[]= $this->aCreateOrder;

		return $this->render(
			'VarelagerBundle:Varelager:historikk.html.twig', 
			array( 
				'departments' => $departments,
				'actionChoices' => $actions
			)
		);
	}

    /**
	 * @Route("/varelager/bestilling", name="_varelager_bestilling")
     * @Template()
     */
	public function bestillingAction(Request $request) {
		$departments = $this->getStorageDepartments(); // populates <select> utsalgssted
		$actions 	= array( $this->aSaveOrder);
		$products 	= $request->request->get('products', null); // json-string
		$action		= $request->request->get('action', null);

		/*
		if (0 == strcmp($action, "createOrder")) {
			$products   = json_decode( $request->request->get('products', null) );
		}
		*/

		return $this->render(
			'VarelagerBundle:Varelager:bestilling.html.twig', 
			array( 
				'postAction' => $action,
				'products' => $products,
				'departments' => $departments,
				'actionChoices' => $actions
			)
		);
	}

    /**
	 * @Route("/varelager/adminpanel", name="_varelager_adminpanel")
     * @Template()
     */
	public function adminpanelAction(Request $request) {

		$submenu = 0;

		$actions[]= $this->aFetchUsers;
		$actions[]= $this->aFetchPerms;
		$actions[]= $this->aFetchProds;
		//$actions[]= $this->aFetchDeps; // no need yet
		$actions[]= $this->aFetchSuppliers;
		$actions[]= $this->aFetchOrders;


		return $this->render(
			'VarelagerBundle:Varelager:adminpanel.html.twig', 
			array( 
				'actionChoices' => $actions
			)
		);

	}


	public function getStorageDepartments() {

		$em 	= $this->getDoctrine()->getEntityManager();
		$query	= $em->createQuery( 
			"SELECT d.id, d.name".
			" FROM DatabaseBundle:Departments d".
			" WHERE d.stockroom='1'");
		$result = $query->getResult($query::HYDRATE_ARRAY);

		return $result;
	}

}
