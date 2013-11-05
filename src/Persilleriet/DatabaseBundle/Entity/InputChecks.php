<?php
namespace Persilleriet\DatabaseBundle\Entity;

class InputChecks {
	// not "" and defined
	public function isDefinedNotEmpy($value) {
		$type = gettype($value);
		if ($value === null ||
				$value === '') { 
			return false; }
		if ($type !== 'boolean' &&
				$type !== 'integer' &&
				$type !== 'double' &&
				$type !== 'string' &&
				$type !== 'unicode') {
			return false; }
		return true;
	}
}
?>
