<?php

namespace Persilleriet\DatabaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\NoResultException;
//use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent; // WAAAAAAAAAAT
use Persilleriet\DatabaseBundle\Controller\CommonController;
use Persilleriet\DatabaseBundle\Entity\Accounts;
use Persilleriet\DatabaseBundle\Entity\Addresses;
use Persilleriet\DatabaseBundle\Entity\EmployeeRoles;
use Persilleriet\DatabaseBundle\Entity\InputChecks;
use Persilleriet\DatabaseBundle\Entity\Orders;
use Persilleriet\DatabaseBundle\Entity\OrdersData;
use Persilleriet\DatabaseBundle\Entity\ProductFilter;
use Persilleriet\DatabaseBundle\Entity\ProductFilterData;
use Persilleriet\DatabaseBundle\Entity\Products;
use Persilleriet\DatabaseBundle\Entity\Stockrecord;
use Persilleriet\DatabaseBundle\Entity\StockrecordData;
use Persilleriet\DatabaseBundle\Entity\Suppliers;
use Persilleriet\DatabaseBundle\Entity\User;
use Persilleriet\DatabaseBundle\Entity\ViewStockrecord;
use JMS\SecurityExtraBundle\Annotation\Secure; // @Secure


use PDO;


class DatabaseController extends CommonController {
	/*
	public function __construct(){
		parent::__construct();
	}
	 */

	private $valid_jsonlists = array('products', 'prodcat' );

	/**
	 * @Route("/get-json/{viewtable}", name="_db_getjson")
	 * FIX permissions...
	 */
	public function getJsonAction($viewtable) {
		return $this->newResponse( $this->sql2json($viewtable), 200, 'json');
    }

	public function sql2json($view) {
		$request= $this->getRequest();
		$em 	= $this->getDoctrine()->getEntityManager();

		// encode preconfigured views as json
		if (in_array($view, $this->valid_jsonlists )) {

			$View = ucfirst($view);
			$query	= $em->createQuery( 
			"SELECT v FROM DatabaseBundle:View$View v");

			$result = $query->getResult($query::HYDRATE_ARRAY);
			return json_encode($result, JSON_FORCE_OBJECT);

		// special cases
		} else if ($view === "roles-unique")	{ return $this->getRolesUnique();
		} else if ($view === "prodcat-unique")	{ return $this->getProductCategoriesUnique();
		} else if ($view === "stockrecord") 	{ return $this->getStockrecordForDate($request);
		} else if ($view === "placedates")		{ return $this->getPlacesDates($request);
		//} else if ($view === "adminUsers")		{ return $this->getAdminPanelUsers();
		//} else if ($view === "adminPermissions"){ return $this->getAdminPanelPermissions();
		//} else if ($view === "adminProducts")	{ return $this->getAdminPanelProducts();
		} else if ($view === "adminDepartments"){ return $this->getAdminPanelDepartments();
		//} else if ($view === "adminSuppliers"){   return $this->getAdminPanelSuppliers();
		} else if ($view === "adminOrders")		{ return $this->getAdminPanelOrders();
		} else if ($view === "formSupplierCategories") { return  $this->getFormSuppCat();
		} else {
			return '{ "error": "Invalid parameter: '.$view.'}';
			//return '{ "moo":"cow says no!"}';
		}

	}

	public function getProductCategoriesUnique() {
		$em 	= $this->getDoctrine()->getEntityManager();
		$query	= $em->createQuery( 
		"SELECT DISTINCT pc.name FROM DatabaseBundle:ProductCategories pc");
		$result = $query->getResult($query::HYDRATE_ARRAY);
		$uniq = array();
		foreach ($result as $v => $k) {
			$uniq[] = $k['name'];
		}
		return json_encode($uniq);
	}

	public function getRolesUnique() {
		$translate = array();
		$translate['ROLE_ROOT']="Boss";
		$translate['ROLE_ADMIN']="Administrator";
		$translate['ROLE_SUPPLIER']="Logistikk";
		$translate['ROLE_MANAGER']="Leder";
		$translate['ROLE_USER']="Medarbeider";

		$em 	= $this->getDoctrine()->getEntityManager();
		$query	= $em->createQuery( 
		"SELECT DISTINCT r.title FROM DatabaseBundle:EmployeeRoles r WHERE r.department is NULL AND r.virtual_role=1");
		$result = $query->getResult($query::HYDRATE_ARRAY);
		$uniq = array();
		foreach ($result as $v => $k) {
			$uniq[$k['title']] = $translate[$k['title']];
		}
		return json_encode($uniq);
	}


	/**
	 * @Route("/get-json/adminusers/{obl}", name="_db_getjson_adminUsers")
	 *  TODO check permissions
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function getAdminPanelUsers($obl) {
		$ret ="";
		try {
		$em		= $this->getDoctrine()->getEntityManager();
		$qb		= $em->createQueryBuilder();
		$qb->addSelect('PARTIAL user.{id, username, nameFirst, nameLast, active},
			address FROM DatabaseBundle:User user JOIN user.address address');
		$order = json_decode( $obl, true );

		$orders=array();

		if ( $order ) {
			foreach ($order as $field => $direction ) {
				if ($this->columnToOrder[$field]) {
					$qb->addOrderBy( $this->columnToOrder[$field], $direction );
				}
			}
		}
		$query	= $em->createQuery( $qb->getDql() );
		//$result = $query->getResult($query::HYDRATE_ARRAY);
		$result = $query->getResult($query::HYDRATE_SCALAR);

		$resultRenamed = array();
		$rename = array(
			'user_id'			=>'id',
			'user_username'		=>'username',
			'user_nameFirst'	=>'nameFirst',
			'user_nameLast'		=>'nameLast',
			'user_active'		=>'active',
			'address_id'		=>'address_id',
			'address_name'		=>'addr_name',
			'address_street'	=>'street',
			'address_zipcode'	=>'zipcode',
			'address_city'		=>'city',
			'address_country'	=>'country',
			'address_phone'		=>'phone',
			'address_email'		=>'email',
			'address_comment'	=>'comment'
		);

		$i = 0;
		foreach ($result as $i => $row) {
			foreach ($row as $column => $record) {
				$resultRenamed[$i][$rename[ $column]] = $record;
				error_log( "column: ".$column  . "rename: ". $rename[$column]);
			}
			$i++;
		}

		//$ret	= json_encode($result);
		$ret	= json_encode($resultRenamed);

		} catch (\Exception $e) {
			error_log( "EX: " . $e->getMessage() );
			return $this->newResponse('Unhandled exception', 500, 'json');
		}
		return $this->newResponse($ret, 200, 'json');
	}

	/**
	 * @Route("/get-json/adminpermissions/{obl}", name="_db_getjson_adminPermissions")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function getJsonAdminPanelPermissionsAction($obl){
		$ret	= "";

		try {
		$em		= $this->getDoctrine()->getEntityManager();
		$qb		= $em->createQueryBuilder();
		$qb->addSelect(
			'partial user.{id, nameFirst, nameLast, username, active}, roles
			FROM DatabaseBundle:User user 
			LEFT JOIN user.roles roles
			LEFT JOIN roles.department department'
		);
		$order = json_decode( $obl, true );
		if ( $order ) {
			foreach ($order as $field => $direction ) {
				if ($this->columnToOrder[$field]) {
					$qb->addOrderBy( $this->columnToOrder[$field], $direction );
				} else {
				}
			}
		}
		$query	= $em->createQuery( $qb->getDql() );
		$result = $query->getResult($query::HYDRATE_ARRAY);
		$ret	= json_encode($result);

		} catch (\Exception $e) {
			error_log( "EX: getJsonAdminProductsAction() " . $e->getMessage() );
			throw $e;
		}
		return $this->newResponse($ret, 200, 'json');
	}

	/**
	 * @Route("/get-json/adminproducts/{obl}", name="_db_getjson_adminProducts")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function getJsonAdminProductsAction($obl) {
	//public function getJsonAdminProductsAction() {
		$ret ="";
		try{

		$em		= $this->getDoctrine()->getEntityManager();
		$qb		= $em->createQueryBuilder();
		$qb->addSelect(
		'p.id as prid, p.name as product, p.partnum, p.price as price, p.expired,  
		 c.id as catid, c.name as category, 
		 s.id as suppid, s.name as supplier
			FROM 
			DatabaseBundle:Products p 
			JOIN p.category c 
			JOIN c.supplier s'
		); 
		$order = json_decode( $obl, true );
		$orders=array(
			'price'=> 'p.price',
			'supplier'=> 's.name',
			'category'=> 'c.name',
			'product'=> 'p.name',
			'partnum'=> 'p.partnum'
		);

		if ( $order ) {
			foreach ($order as $field => $direction ) {
				if ($orders[$field]) {
					$qb->addOrderBy( $orders[$field], $direction );
				} else {
				}
			}
		}
		$query	= $em->createQuery( $qb->getDql() );
		$result = $query->getResult($query::HYDRATE_ARRAY);
		$ret	= json_encode($result);

		} catch (\Exception $e) {
			error_log( "EX: getJsonAdminProductsAction() " . $e->getMessage() );
			throw $e;
		}
		return $this->newResponse($ret, 200, 'json');
    }

	/**
	 * @Route("/get-json/adminsuppliers/{obl}", name="_db_getjson_adminSuppliers")
	 * @Secure(roles="ROLE_ORDER_CLERK")
	 */
	public function getJsonAdminPanelSuppliersAction($obl){
		$ret	="";
		try {
		$em		= $this->getDoctrine()->getEntityManager();
		$qb		= $em->createQueryBuilder();
		$qb->addSelect("v_suppliers FROM DatabaseBundle:ViewSuppliers v_suppliers"); 

		$order 	= json_decode( $obl, true );
		if ( $order ) {
			foreach ($order as $field => $direction ) {
				if ($this->columnToOrder[$field]) {
					$qb->addOrderBy( $this->columnToOrder[$field], $direction );
				} else {
					$col2ob= array(
						'default_cid'=> 'v_suppliers.default_cid',
						'account_number'=> 'v_suppliers.account_number',
						'name' => 'v_suppliers.name',
						'city' => 'v_suppliers.city',
						'zipcode' => 'v_suppliers.zipcode',
						'country' => 'v_suppliers.country',
						'phone' => 'v_suppliers.phone',
						'email' => 'v_suppliers.email',
						'addr_comment' => 'v_suppliers.addr_comment',
						'catnames' => 'v_suppliers.catnames',
						'street' => 'v_suppliers.street'
					);
					$qb->addOrderBy( $col2ob[$field], $direction );
					
				}
			}
		}
		$query	= $em->createQuery( $qb->getDql() );

		//$query	= $em->createQuery( "SELECT v FROM DatabaseBundle:ViewSuppliers v");

		$result = $query->getResult($query::HYDRATE_ARRAY);
		$ret	= json_encode($result);

		} catch (\Exception $e) {
			error_log( "EX: getJsonAdminPanelSuppliersAction() " . $e->getMessage() );
			throw $e;
		}
		return $this->newResponse($ret, 200, 'json');

	}
	public function getAdminPanelOrders(){}
	
	public function getFormSuppCat() {
		$em		= $this->getDoctrine()->getEntityManager();
		$query	= $em->createQuery(
			'SELECT
				s.name as supplier,  s.id as suppid, 
				c.name as category, c.id as catid
			FROM 
			DatabaseBundle:ProductCategories c 
			JOIN c.supplier s 
			ORDER BY s.name, c.name'
		);

		$result = $query->getResult($query::HYDRATE_ARRAY);
		$ret	= json_encode($result);
		return $ret;
	}

	public function getStockrecordForDate(Request $request) {
		$date	= $request->request->get('date', '0000-00-00');
		$depid	= $request->request->getInt('depid', -1);
		$em		= $this->getDoctrine()->getEntityManager();
		$query	= $em->createQuery(
			"SELECT vsr.depid, vsr.department, vsr.prid, vsr.product, vsr.qty,".
			"       vsr.date,  vsr.supplier,   vsr.category, vsr.partnum".
			" FROM DatabaseBundle:ViewStockrecord vsr".
			" WHERE vsr.date=:date AND vsr.depid=:depid")
			->setParameter('date', $date)
			->setParameter('depid', $depid);
		$result = $query->getResult();
		$ret = 	json_encode($result);
		return $ret;
	}
	public function getPlacesDates(Request $request) {

		//$orderORstock 	= $request->request->get('orderORstock', -1);
		//$orderORstock = "stock";
		//if ($orderORstock == "stock") {
			$em	= $this->getDoctrine()->getEntityManager();
			$query	= $em->createQuery(
				'SELECT DISTINCT vs.department, vs.depid, vs.date'.
				' FROM DatabaseBundle:ViewStockrecord vs'.
				' ORDER by vs.depid, vs.date  DESC'
			);

			//$result = $query->getResult($query::HYDRATE_ARRAY);
			$result = $query->getResult();
			$pd = new PlaceDates();
			$pd->build($result);
		//}
		return $pd->__toStringJson();

	}

	/**
	 * @Route("/savestock", name="_db_savestock")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function savestockAction(Request $request) {
		$ret = array(200, 'ok');

		/*
		if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException();
		}
	   */

		$employee	= $request->request->getInt('employee', -1);
		$department = $request->request->getInt('department', -1);
		$action 	= $request->request->get('action', -1);
		$products 	= $request->request->get('products', null, 'deep');

		//$this->get('logger')->info("typeof prods: " . gettype($products) );

		$say = "";
		$say .= "saving...\n";
		$say .= "department: $department\n";
		$say .= "employee: $employee\n";
		//$say .= "action: $action\n";

		/*
		foreach($products as $p) {
			//$say .= "id:" .$p['id']. " qty:" .$p['quantity']. "\n";
			$say .= "id:{$p['id']}  qty:{$p['quantity']} \n";
		}
		*/

		if ($action == 'saveStock') {

			// TODO: enforce employee id of logged in user
			$ret = $this->insertStockRecord($department, $products, $employee);
			if ($ret[0] !== 0) {
				$say .= $ret[1];
			}
		}
		return $this->newResponse($say, $ret[0], 'plain');
    }



	/**
	 * @Route("/saveorder", name="_db_saveorder")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function saveorderAction(Request $request) {
		$ret = array(200, 'ok');
		$say = "";
		$action 	= $request->request->get('action', -1);
		$comment	= $request->request->get('comment', null);
		$products 	= $request->request->get('products', null, 'deep');

		$products	= json_decode($products, true);
		//$this->get('logger')->info(print_r($products, true));
		if ($action == 'saveOrder') {
			$ret = $this->insertOrder($products, $comment);
			if ($ret[0] !== 0) {
				$say .= $ret[1];
			}
		}
		return $this->newResponse($say, $ret[0], 'plain');
	}


	/**
	 * @Route("/saveuser", name="_db_saveuser")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function saveuserAction(Request $request) {
		error_log("saveuserAction()");
		$action 	= $request->request->get('action', -1);
		$params 	= $request->request->get('params', null, 'deep');
		//$params		= json_decode($params, true);

		if ($action !== 'saveUsers') {
			return $this->newResponse( 'invalid request: '.$action, 412, 'plain' );
		} 
		if (count($params) <= 0) { 
			return $this->newResponse('no parameters', 412, 'plain');
		}

		// flatten subarrays
		foreach ($params as $k => $v) {
			if (gettype($v) == 'array') {
				$aa = $v;
				foreach ($v as $kk => $vv) {
					if ($kk == 'id') {
						$params["${k}_$kk"] = $vv;
					} else {
						$params[$kk] = $vv;
					}
				}
				unset($params[$k]);
			}
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_user	= $doc->getRepository('DatabaseBundle:User');
		$r_addr	= $doc->getRepository('DatabaseBundle:Addresses');
		$r_role	= $doc->getRepository('DatabaseBundle:EmployeeRoles');

		try {
			//$newUser = $r_user->findBy(array('id' => $params['id']));
			//$newAddr = $r_addr->findBy(array( 'id' => $params['address_id']));
			$newUser = $r_user->findOneByIdJoinedToAddress($params['id']);
			$em->getConnection()->beginTransaction();
			try {

				if ($newUser) {
					// update
					$newAddr = $newUser->getAddress();
				} else {
					// insert
					$insertedNew = true;
					$basicRole = $r_role->findOneBy(array( 
						'title'=> 'ROLE_USER', 
						'virtual_role'=> 1, 
						'department'=>null));

					$newUser = new User();
					$newAddr = new Addresses();
					$newUser->setActive(true);
					$newUser->setAddress($newAddr);
					$newUser->setNameFirst($params['nameFirst']);
					$newUser->setNameLast($params['nameLast']);
					$newUser->setUsername($params['username']);
					$newUser->addRole($basicRole);

					$randomString = $newUser->generateSalt(8);

					$enc  = $this->get('security.encoder_factory')->getEncoder($newUser);
					$pass = $enc->encodePassword($randomString, $newUser->getSalt() );
					$newUser->setPassword($pass);

				}

				$newAddr->setName($params['nameFirst']. " ".$params['nameLast']);
				$newAddr->setStreet($params['street']);
				$newAddr->setZipcode($params['zipcode']);
				$newAddr->setCity($params['city']);
				$newAddr->setCountry($params['country']);
				$newAddr->setPhone($params['phone']);
				$newAddr->setEmail($params['email']);
				$newAddr->setComment($params['comment']);

				$newUser->setActive($params['active']);
				$newUser->setAddress($newAddr);
				$newUser->setNameFirst($params['nameFirst']);
				$newUser->setNameLast($params['nameLast']);
				$newUser->setUsername($params['username']);

				$em->persist($newAddr); 
				$em->persist($newUser);
				$em->flush();
				$em->getConnection()->commit();
				$lastID = intval( $newUser->getId(), 10 );
		
			} catch (\Exception $e) {
				error_log(print_r($e->getMessage(),true));
				$em->getConnection()->rollback();
				$em->close();
				throw $e;
			}

		} catch (PDOException $pdoe) {
			error_log( "insertOrder(): " .  $pdoe->getMessage() );
			error_log( print_r($e, true) );
			if (strpos($e->getMessage(), 'access violation')) {
				return $this->newResponse($e->getMessage(), 401, 'plain');
			}
			return $this->newResponse($e->getMessage(), 500, 'plain');
		} catch (\Exception $e) {
			error_log( "insertOrder(): " .  $e->getMessage() );
			error_log( print_r($e->getMessage(), true) );
			return $this->newResponse($e->getMessage(), 500, 'plain');
		}

		$resp =  $insertedNew? $this->RESPONSES['user_inserted']: $this->RESPONSES['user_updated'];
		return $this->newResponse( $resp ." id:$lastID", 200, 'plain' );

	}

	/**
	 * @Route("/deleteuser", name="_db_deleteuser")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function deleteuserAction(Request $request) {

		$action 	= $request->request->get('action', -1);
		$params 	= $request->request->get('params', null, 'deep');

		if ($action !== 'deleteUsers') {
			return $this-newResponse('invalid request: '.$action, 412, 'plain');
		} 
		if (count($params) <= 0) { 
			return $this->newResponse('no parameters', 412, 'plain');
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_user	= $doc->getRepository('DatabaseBundle:User');

		try {
			if (count($params)==1) {
				//$user = $r_user->findOneBy(array('id' => $params));
				$user = $r_user->findOneByIdJoinedToAddress( $params);

				error_log( "deleteUsers(): findOneBy id ".$params. ">> ". $user->__toString());
				$em->getConnection()->beginTransaction();

				if ($user) {
					$em->remove( $user);
					$em->flush();
				} else {
					$msg = $this->ERRATA['no_such_user']; 
					error_log( print_r( $user,true));
					$em->getConnection()->rollback();
					$em->close();
					return $this->newResponse($msg,  500, 'plain');
				}
				$em->getConnection()->commit();
			}

		} catch (\Exception $e) {
			error_log("deleteuserAction(): ".$e->getMessage());
			$em->getConnection()->rollback();
			$em->close();

			if ($this->convertSqlError( $e->getMessage())) {
				return $this->newResponse( $this->convertSqlError($e->getMessage()),  405, 'plain');

			} else {
				throw $e;
			}
		}

		return $this->newResponse( $this->RESPONSES['user_deleted'],  200, 'plain');

	}

	/**
	 * @Route("/savepermissions", name="_db_savepermissions")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function savepermissionAction(Request $request) {

		$action 	= $request->request->get('action', -1);
		$params 	= $request->request->get('params', null, 'deep');
		//$params		= json_decode($params, true);

		if ($action !== 'savePermissions') {
			return $this->newResponse('invalid request: '.$action, 412, 'plain');
		} 
		if (count($params) <= 0) { 
			return $this->newResponse('no parameters', 412, 'plain'  );
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_user	= $doc->getRepository('DatabaseBundle:User');
		$r_addr	= $doc->getRepository('DatabaseBundle:Addresses');
		$r_role	= $doc->getRepository('DatabaseBundle:EmployeeRoles');

		try {
			$user = $r_user->findOneBy(array('id' => $params['id']));
			$user->getRoles();
			$em->getConnection()->beginTransaction();
			try {

				if ($user) {
					error_log("removing rolse?");
					$user->removeAllRoles();
					error_log("yep");

					error_log("TITLE:");
					error_log( print_r($params['title'], true));
					if ( gettype( $params['title']) === 'array') {
						for ($i=0; $i< count($params['title']); $i++) {
							$selectedRole = $params['title'][$i];
							$dbRole = $r_role->findOneBy(array( 
								'title'=> $selectedRole, 'virtual_role'=> 1, 'department'=>null));
							$user->addRole($dbRole);
						}
					} else {
							$dbRole = $r_role->findOneBy(array( 
								'title'=> $params['title'], 'virtual_role'=> 1, 'department'=>null));
							$user->addRole($dbRole);
					}
					$user->setActive($params['active']);

				} else {
					throw new Exception('invalid user id');
				}

				$em->persist($user);
				$em->flush();
				$em->getConnection()->commit();
			} catch (\Exception $e) {
				error_log(print_r($e->getMessage(),true));
				$em->getConnection()->rollback();
				$em->close();
				throw $e;
			}

		} catch (PDOException $pdoe) {
			error_log( "savePermissions(): " .  $pdoe->getMessage() );
			error_log( print_r($e, true) );
			if (strpos($e->getMessage(), 'access violation')) {
				return $this->newResponse($e->getMessage(), 401, 'plain');
			}
			return $this->newResponse($e->getMessage(), 500, 'plain');
		} catch (\Exception $e) {
			error_log( "savePermissions(): " .  $e->getMessage() );
			error_log( print_r($e->getMessage(), true) );
			return $this->newResponse($e->getMessage(), 500, 'plain');
		}

		return $this->newResponse('permissions updated', 200, 'plain');
	}



	/**
	 * @Route("/deletesupplier", name="_db_deletesupplier")
	 * @Secure(roles="ROLE_ORDER_CLERK")
	 */
	public function deletesupplierAction(Request $request) {

		$action 	= $request->request->get('action', -1);
		$params 	= $request->request->get('params', null, 'deep');

		if ($action !== 'deleteSuppliers') {
			return $this->newResponse('invalid request: '.$action, 412, 'plain');
		} 
		if (count($params) <= 0) { 
			return $this->newResponse('no parameters', 412, 'plain');
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_supp	= $doc->getRepository('DatabaseBundle:Suppliers');

		try {
			$em->getConnection()->beginTransaction();
			//try {

				if ( count( $params) == 1 )  {
					$supplier = $r_supp->findOneBy( array( 'id' => $params ));
				} 

				if ($supplier) {
					foreach ( $supplier->getCategories() as $k => $cat) {
						$em->remove( $cat );
					}
					$em->remove( $supplier->getAddress() );
					$em->remove( $supplier->getAccount() );
					$em->remove( $supplier );
					$em->flush();
				} else {
					$msg = $this->ERRATA['no_such_supplier']; 
					return $this->newResponse($msg,  500, 'plain');
				}
				$em->getConnection()->commit();
		} catch (\Exception $e) {
			error_log( "deletesupplierAction(): " .  $e->getMessage() );
			$em->getConnection()->rollback();
			$em->close();
			if ($this->convertSqlError( $e->getMessage())) {
				return new Response( 
					$this->convertSqlError($e->getMessage()), 
					405, $this->contentType['plain']);
			} else {
				throw $e;
			}
			return $this->newResponse($e->getMessage(),  500, 'plain');
		}

		return $this->newResponse('supplier deleted',  200, 'plain');
	}

	/**
	 * @Route("/savesupplier", name="_db_savesupplier")
	 * @Secure(roles="ROLE_ORDER_CLERK")
	 */
	public function savesupplierAction(Request $request) {
		$action 	= $request->request->get('action', -1);
		$params 	= $request->request->get('params', null, 'deep');

		if ($action !== 'saveSuppliers') {
			return new Response('invalid request: '.$action, 
				412, $this->contentType['plain']);
		} 
		if (count($params) <= 0) { 
			return new Response('no parameters', 
				412, $this->contentType['plain']);
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_supp	= $doc->getRepository('DatabaseBundle:Suppliers');
		$r_pcat	= $doc->getRepository('DatabaseBundle:ProductCategories');

		try {
			$supplier = $r_supp->findOneByIdFullyJoined($params['id']); 
			// verify sane category-chooices or add new categories
			$definedCats = array();
			$paramCats =  array();
			$removedCats = array();

			// convert to uppercase
			$paramCats =  explode(',',$params['catnames']);
			for ($i=0; $i< count($paramCats); $i++) {
				$paramCats[$i] = strtoupper(trim($paramCats[$i]));
			}
			
			if ($supplier) {
				foreach ($supplier->getCategories() as $ck => $cat) {
					$definedCats[]= $cat->getName();
				}
				// removeList = definedList - inputList
				foreach ($definedCats as $k => $dcat) {
					if (! in_array($dcat, $paramCats )) {
						$removedCats[]= $dcat;
					}
				}
				foreach ($removedCats as $k => $catname) {
					$validCat = $r_pcat->findOneBySupplierIdAndCategoryName( 
						$params['id'], $catname);
					if ($validCat) { // it's ok to remove. 
					} else {
						return new Response(
							$this->ERRATA['category_not_found'], 
							412, $this->contentType['plain']);
					}
				}
			}
			error_log( "defined: ". implode(',', $definedCats) );
			error_log( "add    : ". implode(',', $paramCats) );
			error_log( "remove : ". implode(',', $removedCats) );
			
			$em->getConnection()->beginTransaction();


			if ($supplier) {
				// update
				$account = $supplier->getAccount();
				$address = $supplier->getAddress();
			} else {
				// insert
				$supplier = new Suppliers();
				$account = new Accounts();
				$address = new Addresses();
				$supplier->setAddress( $address);
				$supplier->setAccount( $account);

			}

			foreach ($removedCats as $k => $catname) {
				$em->remove( $supplier->removeCategoryWithName($catname));
			}
			foreach ($paramCats as $k => $catname) {
				if ($catname != '') { 
					$supplier->addUniqueCategory($catname); 
				}
			}

			$supplier->setName( $params['name']);
			$supplier->getAccount()->setCustomerIdentification( $params['default_cid']);
			$supplier->getAccount()->setName( $params['name']);
			$supplier->getAccount()->setNumber( $params['account_number']);
			$supplier->getAddress()->setName( $params['name']);
			$supplier->getAddress()->setStreet( $params['street']);
			$supplier->getAddress()->setZipcode( $params['zipcode']);
			$supplier->getAddress()->setCity( $params['city']);
			$supplier->getAddress()->setCountry( $params['country']);
			$supplier->getAddress()->setPhone( $params['phone']);
			$supplier->getAddress()->setEmail( $params['email']);
			$supplier->getAddress()->setComment( $params['addr_comment']);


			$em->persist($supplier);
			$em->persist($account);
			$em->persist($address);
			foreach ($supplier->getCategories() as $k => $cat) {
				$em->persist( $cat);
			}

			$em->flush();
			$em->getConnection()->commit();

			$lastSupplier = intval( $supplier->getId(), 10 );

		} catch (\Exception $e) {
			error_log( "saveSuppliers(): " .  $e->getMessage() );
			$em->getConnection()->rollback();
			$em->close();
			return $this->newResponse($e->getMessage(),  500, 'plain');
		}
		return $this->newResponse( $this->RESPONSES['supplier_saved'] ." id:$lastSupplier",  200, 'plain');
	}

	/**
	 * @Route("/", name="_db_root")
	 */
	public function indexAction() {

		//$db = new DatabaseBundle();

		$uri = $this->getRequest()->getRequestUri();
		$msg = "<!DOCTYPE html><html><head><title>DB get-json</title></head><body>\n";
		$msg .= "Please try one of the following:<br />\n<pre>";


		foreach ($this->valid_jsonlists as $k=>$v) {
			$a = "<a href='".$uri."get-json/$v" ."'>$v</a>\n";
			$msg .= $a;
		}
		$msg .= "<a href='".$uri."get-json/prodcat-unique" ."'>prodcat-unique</a>\n";
		$msg .= "<a href='".$uri."get-json/placedates" ."'>placedates</a>\n";
		$msg .= "<a href='".$uri."get-json/adminusers/k" ."'>admin Users</a>\n";
		$msg .= "<a href='".$uri."get-json/adminproducts/k" ."'>admin Products</a>\n";
		$msg .= "<a href='".$uri."get-json/adminpermissions/k" ."'>admin Permissions</a>\n";
		$msg .= "<a href='".$uri."get-json/adminsuppliers/k" ."'>admin Suppliers</a>\n";
		$msg .= "<a href='".$uri."get-json/formSupplierCategories" ."'>formSupplierCategories</a>\n";
		$msg .= "</pre></body></html>";

		return $this->newResponse($msg, 200, 'html');

	}

	/* returns array( int http response code, string message) */
	public function insertUser($params) {

	}

	/*
	 * returns array( int http response code, string message)
	 */
	public function insertOrder($products, $comment) {

		if (count($products) <= 0) {
			return array(412, 'Refusing to save an empty order');
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();

		//$r_prod_filter	= $doc->getRepository('DatabaseBundle:ProductFilter');
		//$r_dep			= $doc->getRepository('DatabaseBundle:Departments');
		$r_prod 		= $doc->getRepository('DatabaseBundle:Products');

		//$filterForDep 	= $r_dep->find($department); // returns null if department=0 ? 

		$logger = $this->get('logger');
		$logger->info("INSERTING ORDER");

		try {


			$newOrder 	= new Orders();
			$newOrder->setComment($comment);
			$newOrder->setDate(new \Datetime());

			$em->persist($newOrder);
			$em->flush(); // remove this ?

			//$record	= $em->find('DatabaseBundle:Stockrecord', $newStockrecord->getId());
			$lastInsertedFilter	= $newOrder->getId();

			$logger->info("ENTERING FOREACH");
			$logger->info("LAST INSERTED ORDER: " . $lastInsertedFilter);

			foreach ($products as $prod) {
				if (	(intval($prod['prid']) >0) && 
						(intval($prod['quantity'][0]['qty']) > 0)) {

					$product 		= $r_prod->find($prod['prid']);
					$orders_data 	= new OrdersData();

					$logger->info("ORDER-PROD: " . $product->getId());

					$orders_data->setOrder($newOrder);
					$orders_data->setProduct($product);
					$orders_data->setQuantity($prod['quantity'][0]['qty']);

					$logger->info("OD: " . $orders_data->__toString() );

					$em->persist($orders_data);

				} else {
					//throw new Exception($errorMsg);
					//$dbh->rollBack();
					return array( 417, "insertOrder(): prod.qty < 0 OR prod.id < 0");
				}
			}

			$logger->info("LEAVING FOREACH");
			$em->flush();

		} catch (PDOException $pdoe) {
			error_log( "insertOrder(): " +  $pdoe->getMessage() );
			if (strpos($e->getMessage(), 'access violation')) {
				return array( 401, $e->getMessage() );
			}
			return array( 500, $e->getMessage()) ;
		} catch (Exception $e) {
			error_log( $e->getMessage() );
			return array(500, $e->getMessage());
		}
		return array(200, 'order saved');

	}


	/*
	 * returns array( int http response code, string message)
	 */
	public function insertStockrecord($department, $products, $employee) {


		if ((intval($department) <= 0 )||(intval($employee) <= 0)) {
			//throw new Exception($errorMsg);
			return array(412, 'insertStockrecord(): Invalid department or employee id ');
		}

		if (count($products) <= 0) {
			return array(412, 'Refusing to save an empty list');
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();

		$r_emp	= $doc->getRepository('DatabaseBundle:User');
		$r_dep	= $doc->getRepository('DatabaseBundle:Departments');
		$r_prod = $doc->getRepository('DatabaseBundle:Products');
		$r_stockrecord	
				= $doc->getRepository('DatabaseBundle:Stockrecord');
		$r_stockrecordData
				= $doc->getRepository('DatabaseBundle:StockrecordData');

		//$query= $em->createQuery( 
		//	"SELECT * FROM DatabaseBundle:View".ucfirst($view));
		//$result = $query->getResult($query::HYDRATE_ARRAY);

		$last = 0;

		try {
			$responsiblePerson 	= $r_emp->find($employee);
			$fromDepartment 	= $r_dep->find($department);
			// makes a 
			$newStockrecord 	= new Stockrecord();
			$newStockrecord->setDate( new \Datetime() );
			$newStockrecord->setEmployee($responsiblePerson);
			$newStockrecord->setDepartment($fromDepartment);

			//$stmt = $this->dbh->prepare( 
			//"INSERT INTO stockrecord (date, employee_id, department_id) VALUES (NOW(), ?, ?)");
			//$stmt->execute(array( $employee, $department));
			//$last = $this->dbh->lastInsertId();
			

			$logger = $this->get('logger');
			$logger->info("BEFORE INSERT nsr id:");
			$logger->info($newStockrecord->getId());
			$em->persist($newStockrecord);
			$em->flush();

			$last = $em->getConnection()->lastInsertId();
			$last = intval($last, 10);

			$logger->info("AFTER INSERT nsr id:");
			$logger->info($newStockrecord->getId());

			$logger->info("LAST");
			$logger->info($last);

		} catch (\Exception $e) {
			error_log( "e" );
			error_log( "e" );
			error_log( "e" );
			error_log( "e" );
			error_log( "e" );
			error_log( $e->getMessage() );

			if (strpos($e->getMessage(), 'access violation')) {
				return array( 401, $e->getMessage() );
			}
			return array(500, $e->getMessage());

		}


		try {


			//$record	= $r_stockrecord->find($newStockrecord->getId());
			$record	= $em->find('DatabaseBundle:Stockrecord', $newStockrecord->getId());

			foreach($products as $prod) {
				if (	(intval($prod['id']) > 0 ) && 
						(intval($prod['quantity']) > 0 )) { // maybe they want to save qty= 0? 

					$product 		= $r_prod->find($prod['id']);
					$record_data 	= new StockrecordData();

					$msg = "\nFOUND PRODUCT(".$product->getId()."): " . $product->getName();
					$msg .= "\nFOUND RECORD(".$record->getId().") " ;
					$logger->info($msg);

					$record_data->setStockrecord($record);
					$record_data->setProduct($product);
					$record_data->setQuantity($prod['quantity']);

					//$record->addStockrecord($record_data);

					//$em->persist($record);
					$em->persist($record_data);


					//$stmt = $this->dbh->prepare( 
					//'INSERT INTO stockrecord_data (stockrecord_id, product_id, quantity)'.
					//' VALUES (?, ?, ?)');
					//$stmt->execute(array( $last, $prod['id'], $prod['quantity']));


				} else {
					//throw new Exception($errorMsg);
					//$dbh->rollBack();
					return array( 417, "insertStockrecord(): prod.id OR prod.qty < 0");
				}
			}
			$em->flush();
			//$this->dbh->commit();

		} catch (PDOException $pdoe) {
			$dbh->rollBack();
			error_log( "insertStockrecord(): " +  $pdoe->getMessage() );
			return array( 500, $e->getMessage()) ;
		}
		return array(200, 'saved');
	}


}

class PlaceDates {
	private $places;

	public function build($result) {
		foreach ($result as $r) {
			$this->addDate($r['depid'], $r['department'], $r['date']);
		}
	}

	public function addPlace($id, $place) {
		$c = count($this->places);
		$notfound = true;
		for ($i=0; $i<$c; $i++) {
			if ($this->places[$i]['depid'] == $id) {
				$i = $c;
				$notfound = false;
			}
		}
		if ($notfound)  {
			$this->places[]= array(
				'depid'			=> $id, 
				'department'	=> $place, 
				'dates'		 	=> array());
		}
		return $notfound;
	}
	public function addDate($id, $place, $date) {
		$this->addPlace($id, $place);
		$c = count($this->places);
		for ($i=0; $i<$c; $i++){
			if ($this->places[$i]['depid'] == $id) {
				$this->places[$i]['dates'][]= $date;
				$i = $c;
			}
		}
	}
	public function __toStringJson(){
		return json_encode($this->places);
	}
	public function __toString(){
		$msg = "";
		foreach ($this->places as $place){
			$msg .= "{$place['depid']}: {$place['department']}\n";
			foreach ($place['dates'] as $date) {
				$msg .= "\t$date\n";
			}
		}
		return $msg;
	}

}

?>
