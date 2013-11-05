<?php
namespace Persilleriet\DatabaseBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure; // @Secure

class TestProductController extends CommonController {
	private $html  = '<body style="font-face:monospace;font-size:8"><pre>';

	/**
	 * @Route("/test/product/list/{id}", name="_test_db_product")
	 * @Secure(roles="ROLE_ROOT")
	 */
	public function indexAction($id) {
		try 
		{

			$em		= $this->getDoctrine()->getEntityManager();
			$query	= $em->createQuery('
				SELECT	p,f
				FROM 	DatabaseBundle:Products p 
				LEFT 
				JOIN	p.filters f 
				WHERE	p.id = :id 
			')->setParameter('id', $id);
			$result = $query->getResult($query::HYDRATE_ARRAY);
			$ret 	= print_r( $result, true);

			return new Response(
				sprintf("%s\n%s", $this->html, htmlentities($ret==null?'null':$ret) ), 
				200, $this->contentType['html']);

		} 
		catch (\Exception $e) 
		{
			$this->error_log_exception_from_method($e);
		}
	}
	/**
	 * @Route("/test/product/delete/{id}")
	 * @Secure(roles="ROLE_ROOT")
	 */
	public function deleteAction($id) {
		try 
		{
			$em		= $this->getDoctrine()->getEntityManager();
			$r_prod	= $this->getDoctrine()->getRepository('DatabaseBundle:Products');

			$product = $r_prod->findOneById( $id );
			$ret 	= print_r( $product, true);
			//$ret = json_encode($product);
			//error_log( print_r( $product, true));

			$ret = sprintf(" %s\n", $product );
			for ($i=0; $i< $product->getFilters()->count(); $i++) {
				$f = $product->getFilters()->get($i);
				$ret .= "   \\".$f."\n";
			}


			return new Response(
				sprintf("%s\n%s", $this->html, htmlentities($ret==null?'null':$ret) ), 
				200, $this->contentType['html']);

			/*
			return new Response(
				$ret,
				200, $this->contentType['json']);
			*/

		}
		catch (\Exception $e) 
		{
			$this->error_log_exception_from_method($e);

		}

	}
	
}
