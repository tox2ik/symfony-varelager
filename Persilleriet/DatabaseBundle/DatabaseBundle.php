<?php

namespace Persilleriet\DatabaseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PDO;
use Exception;

class DatabaseBundle extends Bundle {


	//private $hostname = 'genja.lan';
	//private $hostname = 'localhost';
	//private $dbname = 'persilleriet';
	//private $dbh = null;


	function __construct() {

		//$username = 'priet_read';
		//$password = 'saltstenger';
		//$this->connect($username, $password);
		//$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	function __destruct() {
		$this->dbh = null;
	}

/*
	public function connect($user, $pass) {
		$this->dbh = new PDO(
			"mysql:host=$this->hostname;".
			"dbname=$this->dbname", 
			$user, $pass);
	}
*/


	public function insertStockrecord($department, $products, $employee) {

		$username = 'priet_stock';
		$password = 'saltstenger';
		$this->connect($username, $password);

		$errorMsg = "Unexpected input values in insertStockrecord()";
		if ((intval($department) <= 0 )||(intval($employee) <= 0)) {
			throw new Exception($errorMsg);
		}

		$last = 0;


		try {
			$stmt = $this->dbh->prepare( 
			"INSERT INTO stockrecord (date, employee_id, department_id) VALUES (NOW(), ?, ?)");
			$stmt->execute(array( $employee, $department));
			$last = $this->dbh->lastInsertId();

		} catch (Exception $e) {
			error_log( $e->getMessage() );

			if (strpos($e->getMessage(), 'access violation')) {
				return array( 401, $e->getMessage() );
			}
			return array(500, $e->getMessage());

		}


		try {

			$this->dbh->beginTransaction();
			foreach($products as $prod) {
				if ((intval($prod['id']) !=0) && (intval($prod['quantity'])!= 0)) {
					$stmt = $this->dbh->prepare( 
					'INSERT INTO stockrecord_data (stockrecord_id, product_id, quantity)'.
					' VALUES (?, ?, ?)');
					$stmt->execute(array( $last, $prod['id'], $prod['quantity']));

				} else {
					//throw new Exception($errorMsg);
					$dbh->rollBack();
					return array( 417, $errorMsg);
				}
			}
			$this->dbh->commit();

		} catch (PDOException $pdoe) {
			$dbh->rollBack();
			error_log( "insertStockrecord(): " +  $pdoe->getMessage() );
			return array( 500, $e->getMessage()) ;
		}
		return array(200, 'saved');
	}





}
?>
