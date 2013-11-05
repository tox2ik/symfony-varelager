<?php

namespace Persilleriet\DatabaseBundle\Controller;
use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure; // @Secure
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\NoResultException;


use Persilleriet\DatabaseBundle\Entity\Suppliers;
use Persilleriet\DatabaseBundle\Entity\User;
use Persilleriet\DatabaseBundle\Entity\ProductFilter;
use Persilleriet\DatabaseBundle\Entity\ProductFilterData;
/*
use Persilleriet\DatabaseBundle\Entity\Accounts;
use Persilleriet\DatabaseBundle\Entity\Addresses;
use Persilleriet\DatabaseBundle\Entity\EmployeeRoles;
use Persilleriet\DatabaseBundle\Entity\InputChecks;
use Persilleriet\DatabaseBundle\Entity\Orders;
use Persilleriet\DatabaseBundle\Entity\OrdersData;
use Persilleriet\DatabaseBundle\Entity\Products;
use Persilleriet\DatabaseBundle\Entity\Stockrecord;
use Persilleriet\DatabaseBundle\Entity\StockrecordData;
use Persilleriet\DatabaseBundle\Entity\ViewStockrecord;
 */

class FilterController extends CommonController {


	/**
	 * @Route("/listfilter/{filterid}", name="_db_listfilter")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function listFilterAction($filterid){
		$em = $this->getDoctrine()->getEntityManager();

		$query= $em->createQuery( 
			//'SELECT f,p FROM DatabaseBundle:ProductFilter f JOIN f.products p WHERE f.id=:id'
			'SELECT f,p,c,s FROM DatabaseBundle:ProductFilter f 
			JOIN f.products p 
			JOIN p.category c 
			JOIN c.supplier s 
			WHERE f.id=:id'
		)->setParameter('id', $filterid);

		try {

			$result = $query->getResult($query::HYDRATE_ARRAY);
			$ret = json_encode($result);
			return $this->newResponse($ret,  200, 'json');

		} catch (\Exception $e) {
			error_log("listFilterAction EX: ". $e->getMessage() );
		}


	}
	/**
	 * @Route("/listfilters/{format}", name="_db_listfilters_dialog")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function listFilterDialogAction($format){
		$em = $this->getDoctrine()->getEntityManager();
		$query= $em->createQuery( 
			'SELECT d.name as department, 
				pf.id as id,        
				pf.name as filter,
				pf.comment as comment,
				count(p) as prod_count
			FROM DatabaseBundle:ProductFilter pf
			JOIN pf.products p
				LEFT JOIN pf.department d
			GROUP BY pf.id'
		);

		try {
			$result = $query->getResult($query::HYDRATE_SCALAR);
			$ret ='';
			if ($format === 'json') {
				$ret = json_encode($result);
				$contentType = 'json';
			} else if ($format === 'table') {

				//$ret = "<table><tbody>\n";
				$ret .= "<table><thead><tr><th>Avdeling</th><th>Tittel</th><th>Kommentar</th></tr></thead><tbody>\n";
				foreach ($result as $k => $r) {
					$ret .= sprintf(
							'<tr id="filter%d"><td><span title="%s">%s</span></td>'.
							'<td><span title="%s">%s (%d)</span></td>'.
							"<td><span title=\"%s\">%s</span></td></tr>\n",
						$r['id'],  
						$r['department'],	$this->wordsMaxLength($r['department'],	50),
						$r['filter'],		$this->wordsMaxLength($r['filter'],  	20), 	 $r['prod_count'],
						$r['comment'],		$this->wordsMaxLength($r['comment'], 	40)
					);
				}
				$ret .= "</tbody></table>";
				$contentType = 'html';

			}

			return $this->newResponse($ret, 200, $contentType );
		} catch (\Exception $e) {
			error_log("listFilterAction EX: ". $e->getMessage() );
		}

	}



	/**
	 * @Route("/savefilter", name="_db_savefilter")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function savefilterAction(Request $request) {

		$params['action'] 		= $request->request->get('action', -1);
		$params['department']	= $request->request->getInt('department', -1);
		$params['products']		= $request->request->get('products', null, 'deep');
		$params['name']			= $request->request->get('name','Unnamed Filter');
		$params['comment']		= $request->request->get('comment','');
		//$params['filter_name'] 	= $date->format('c');
		$params['created']		= new DateTime();

		error_log('mooo 1');

		//echo $date->format('c'); // 2012-10-04T21:10:02+02:00

/*
		$errorMsg = "Unexpected input values in insertProductFilter()";
		if ( intval($department) < 0 ){
			error_log('mooo 1.01');
			//throw new Exception($errorMsg);
			$resp = new Response('insertProductFilter: Invalid department id ', 412, $this->contentType['plain']);
			error_log('mooo 1.02');
			return  $resp;
		}
		try {
		} catch (\Exception $e) {
			error_log('savefilterAction():'  . $e->getMessage());
		}

*/

		if (! $params['name']){
			return $this->newResponse($this->ERRATA['bad_param_nameless_Filter'],  405, 'plain');
		}
		if (count($params['products']) <= 0) {
			return $this->newResponse($this->ERRATA['bad_param_empty_filter'],  405, 'plain');
		}


		$doc	= $this->getDoctrine();
		$em		= $doc->getEntityManager();
		$r_dep	= $doc->getRepository('DatabaseBundle:Departments');
		$r_prod = $doc->getRepository('DatabaseBundle:Products');

		error_log("department int: " . $params['department']);

		error_log('mooo 2');
		try { 
			$em->getConnection()->beginTransaction();

			$newDepartment 	= $r_dep->find($params['department']); 
			$newFilter 		= new ProductFilter();
			$insertedNew	= true;

			$newFilter->setName($params['name']);
			$newFilter->setComment($params['comment']);
			$newFilter->setCreated($params['created']);
			//$newFilter->setDate($params['date'])
			if ($newDepartment != null) {
				$newFilter->setDepartment($newDepartment);
			}

			$em->persist($newFilter);
			$em->flush(); // remove this ?

			$lastInsertedFilter	= $newFilter->getId();

			$productIDs = array();
			foreach( $params['products'] as $k => $prod) {
				if (intval( $prod['id'] ) > 0) {
					$productIDs[]= intval(  $prod['id']) ;
				}
			}

			$query = $em->createQuery(
				'SELECT p FROM DatabaseBundle:Products p WHERE p.id IN(:plist)'
			)->setParameter('plist', $productIDs);
			$productList = $query->getResult();

			foreach ($params['products'] as $prod) {
				if (	(intval($prod['id']) >0) && 
						(intval($prod['quantity']) >= 0)) {
					for ($i=0; $i< count($productList); $i++) {
						if ($productList[$i]->getId() == $prod['id']) {
							$pfd = new ProductFilterData();
							$pfd->setProductFilter($newFilter);
							$pfd->setProduct($productList[$i]);
							$pfd->setQuantity($prod['quantity']);
							$em->persist($pfd);
							$i = count($productList);
							unset($productList[$i]);
						}
					}
				} else {
					throw new \Exception('bad_parameter_for_quantity_or_product');
				}
			}
			$em->flush();
			$em->getConnection()->commit();
			
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			$em->close();
			error_log( "savefilterAction() EX: " .$e->getMessage() );
			$msg = $e->getMessage();
			if ($e->getMessage() == 'bad_parameter_for_quantity_or_product'){
				$msg = $this->ERRATA['bad_parameter_for_quantity_or_product'];
				return $this->newResponse($msg,  412, 'plain');
			}
			return $this->newResponse($msg,  500, 'plain');
		}

		$resp = $insertedNew? $this->RESPONSES['filter_inserted'] : 
			$this->RESPONSES['filter_updated'];

		return $this->newResponse( $resp,  200, 'plain');
	}
	private function wordsMaxLength($sentence = '', $len = 80) {
		if ($sentence == null||$sentence === '') {return '';}
		$words = explode(' ',$sentence);
		$truncated = "";
		$firstWord  = array_splice($words,0,1);
		$truncated .= $firstWord[0]. " ";
		while (( count($words) > 0  )&&( strlen($truncated) < $len - 3)) {
			$firstWord  = array_splice($words,0,1);
			$truncated .= $firstWord[0] . " ";
		}
		/*
		if (count(explode(' ',$truncated))==1) {
			$truncated = substr($truncated,0, $len-3);
		}*/
		if (strlen($truncated)+1 > $len ||
				count($words) != 0 ) {
			$truncated = trim($truncated) ."...";
		}
		return $truncated;
	}

}
