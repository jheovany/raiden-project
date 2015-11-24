<?php

namespace Raiden;

class JsonEncoder {
	

	private $json;

	function __construct ( ) {
		

	}

	public function initialize ( $values ) {

		$this->json = json_encode($values);
	}

	public function getJson() {

		return $this->json;
	}
}