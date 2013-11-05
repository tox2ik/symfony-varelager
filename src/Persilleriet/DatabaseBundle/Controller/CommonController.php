<?php
namespace Persilleriet\DatabaseBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CommonController extends Controller {
	protected $RESPONSES = array ( 
		'user_deleted'=> 'Brukeren ble slettet',
		'user_updated'=> 'Bruker oppdatert',
		'user_inserted'=> 'Ny bruker lagret',
		'supplier_saved'=> 'Leverandøren ble lagret',
		'product_deleted'=> 'Varen ble slettet',
		'product_updated'=> 'Vareinformasion oppdatert',
		'product_inserted'=> 'Ny vare ble lagt til',
		'filter_inserted'=> 'Produktfilteret ble lagret',
		'filter_updated'=>'Endringer til produktfilteret ble bevart',

		'msg'=> 'norwegian'
	);
	protected $ERRATA = array ( 
		'prod_used_in_stockrecord'=> 'Produktet er oppført i en varetelling.',
		'no_such_product'=> 'Produktet finnes ikke.',
		'no_such_user'=> 'Brukeren finnes ikke.',
		'refusing_to_save_empty_product_filter'=>'Et produktfilter må inneholde en eller flere varer.',
		'no_such_category_for_given_supplier' => 'Denne produktkategorien er ikke opprettet for gitt leverandør.',
		'delete_denied'=> 'Fikk ikke tilgang til å slette innhold i tabeller (sjekk MySQL rettigheter).',
		'user_has_done_stockrecords'=> 'Brukeren er oppført som ansvarlig for en eller flere varetellinger.',
		'supplier_has_products'=> 'Kan ikke fjerne leverandør så lenge det er oppført produkter under leverandøren.',
		'bad_parameter_for_quantity_or_product' => 'Ugyldig produktmengde eller produkt-id. Filteret ble ikke lagret',
		'bad_param_nameless_Filter' => 'Filteret kan ikke lagres uten et navn',
		'bad_param_empty_filter' => 'Et produktfilter må inneholde minst ett produkt.',
		'bad_param_category_not_found'=> 'Oppgitt kategori er ikke opprettet for denne leverandøren',
		'msg'=> 'norwegian'
	);
	protected $columnToOrder = array(
		'price'=> 'product.price',
		'supplier'=> 'supplier.name',
		'category'=> 'category.name',
		'product'=> 'product.name',
		'partnum'=> 'product.partnum',

		'nameFirst'=> 'user.nameFirst',
		'nameLast'=> 'user.nameLast',
		'username'=> 'user.username',
		'street'=> 'address.street',
		'zipcode'=> 'address.zipcode',
		'city'=> 'address.city',
		'country'=> 'address.country',
		'phone'=> 'address.phone',
		'email'=> 'address.email',
		'comment'=> 'address.comment'

	);
	protected $contentType = array (
		'json'  => array('Content-Type' => 'application/json;charset=utf-8'),
		'plain' => array('Content-Type' => 'text/plain'),
		'html' => array('Content-Type' => 'text/html')
	);

	public function convertSqlError($subject) {
		//SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'libø' for key 'username'

		$pattern = "/SQLSTATE.*23000.*1451.*stockrecord_data_fk_product_id.*/";
		if ( preg_match( $pattern, $subject) ) {
			return $this->ERRATA['prod_used_in_stockrecord'];
		}

		$pattern = "/SQLSTATE.*23000.*1451.*stockrecord_fk_eployee_id.*/";
		if ( preg_match( $pattern, $subject) ) {
			return $this->ERRATA['user_has_done_stockrecords'];
		}

		$pattern = "/SQLSTATE.*23000.*1451.*products_category_id.*/";
		if ( preg_match( $pattern, $subject) ) {
			return $this->ERRATA['supplier_has_products'];
		}

		$pattern = "/SQLSTATE.*42000.*1142.*DELETE.*/";
		if ( preg_match( $pattern, $subject) ) {
			return $this->ERRATA['delete_denied'];
		}

		return null;
	}
	protected function  error_log_exception_from_method(\Exception $e) {

		$trace=debug_backtrace();
		$caller=array_shift($trace);
		$caller=array_shift($trace);

		error_log( sprintf('EX: %s(): %s', $caller['function'], $e->getMessage()) );

		/*
		echo "Called by {$caller['function']}";
		if (isset($caller['class']))
		echo " in {$caller['class']}";
		 */
	}
	protected function newResponse($strMessage, $iCode, $strSimpleType) {
		// strSimpleType : text, html, json
		$response = new Response($strMessage, $iCode, $this->contentType[$strSimpleType]);
		$response->headers->set('X-Content-Type-Options', 'nosniff'); 
		// internet explorer: disable auto detection of contents i.e. tust headers.
		return $response;
	}
}
