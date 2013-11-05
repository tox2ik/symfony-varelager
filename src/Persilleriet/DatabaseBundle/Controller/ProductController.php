<?php
namespace Persilleriet\DatabaseBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure; // @Secure
use Persilleriet\DatabaseBundle\Entity\Products;


class ProductController extends CommonController {

	/**
	 * @Route("/saveproduct", name="_db_saveproduct")
	 * @Secure(roles="ROLE_ORDER_CLERK")
	 */
	public function saveproductAction(Request $request) {
		$action 	= $request->request->get('action', -1);
		$params 	= $request->request->get('params', null, 'deep');
		//$params		= json_decode($params, true);

		if ($action !== 'saveProducts') {
			return $this->newResponse('invalid request: '.$action, 412, 'plain');
		} 
		if (count($params) <= 0) { 
			return $this->newResponse('no parameters', 412, 'plain');
		}

		$insertedNew = false;
		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_prod	= $doc->getRepository('DatabaseBundle:Products');
		$r_pcat	= $doc->getRepository('DatabaseBundle:ProductCategories');

		try {
			$product = $r_prod->findOneBy(array('id' => $params['prid']));
			$em->getConnection()->beginTransaction();
/*
			try {
				error_log( print_r($params,true) );
*/

			if ($product) {
				// set categoty
				$category = $r_pcat->findOneBy( array('id'=> $params['catid']));
				if (! $category) {
					error_log("could not find prodcat with id:" . $id);
				}
				//$params['catid']
				$product->setName($params['product']);
				$product->setPartnum( $params['partnum']);
				$product->setExpired( $params['expired']);
				$product->setPrice( $params['price']);
				$product->setCategory( $category);

			} else {
				$insertedNew = true;
				$product = new Products();
				$category = $r_pcat->findOneBy( array('id'=> $params['catid']));
				// check ids cat.suppid = param[suppid] && cat.catid == parap[catid]
				if ( $category->getId() != $params['catid'] || 
						$category->getSupplierId() != $params['suppid']){
					throw new \Exception('no_such_category_for_given_supplier');	
				}


				$product->setName($params['product']);
				$product->setPartnum( $params['partnum']);
				$product->setExpired( $params['expired']);
				$product->setPrice( $params['price']);
				$product->setCategory( $category);

				//throw new \Exception('failed to insert product');
			}

			$em->persist($product);
			$em->flush();
			$em->getConnection()->commit();
			$lastID = $product->getId();

		} catch (\Exception $e) {
			error_log( "saveproductAction(): " .  $e->getMessage() );
			error_log( print_r($e->getMessage(), true) );
			$em->getConnection()->rollback();
			$em->close();
			if ($e->getMessage() == 'no_such_category_for_given_supplier') {
				return $this->newResponse( $this->ERRATA[$e->getMessage()],  412, 'plain');
			}
			return $this->newResponse($e->getMessage(),  500, 'plain');
		}

		$resp = $insertedNew? 
				$this->RESPONSES['product_inserted'] . " id:$lastID"
				: $this->RESPONSES['product_updated']  . " id:$lastID";
		return $this->newResponse( $resp,  200, 'plain');
	}


	/**
	 * @Route("/deleteproduct", name="_db_deleteproduct")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function deleteproductAction(Request $request) {

		$action 	= $request->request->get('action', -1);
		$params 	= $request->request->get('params', null, 'deep');

		if ($action !== 'deleteProducts') {
			return $this->newResponse('invalid request: '.$action, 412, 'plain');
		} 
		if (count($params) <= 0) { 
			return $this->newResponse('no parameters', 412, 'plain');
		}

		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_prod	= $doc->getRepository('DatabaseBundle:Products');
		//$r_pcat	= $doc->getRepository('DatabaseBundle:ProductCategories');
		//
		try {


			$em->getConnection()->beginTransaction();
			//try {

				if ( count( $params) == 1 )  {
					error_log("one prod: " . $params );
					$product = $r_prod->findOneBy( array( 'id' => $params ));
				} 

				if ($product) {
					$em->remove( $product );
					error_log('em->remove(): done');
					$em->flush();
					//error_log('em->flush(): done');
				} else {
					//throw new \Exception('failed to find product');
					$msg = $this->ERRATA['no_such_product']; 
					$msg .= "\n(listen må oppdateres)";
					return $this->newResponse($msg,  500, 'plain');
				}
				$em->getConnection()->commit();
				error_log( '$em->getConnection()->commit(): done');
		} catch (\Exception $e) {
			error_log( "deleteproductAction(): " .  $e->getMessage() );
			$em->getConnection()->rollback();
			$em->close();
			if ($this->convertSqlError( $e->getMessage())) {
				return $this->newResponse( $this->convertSqlError($e->getMessage()),  405, 'plain');
			} else {
				throw $e;
			}
			return $this->newResponse($e->getMessage(),  500, 'plain');
		}

		return $this->newResponse('product deleted',  200, 'plain');
	}
}
