<?php

namespace Raiden\TestModels;

/**
 * @table invoice_details
 */
class InvoiceDetails {

	/**
	 * @field id
	 * @PK
	 */
	private $id;

	/**
	 * @field id_concept
	 * @hasone Raiden\TestModels\Concept
	 */
	private $concept;

	/**
	 * @field total
	 * @type double
	 */
	private $total;

	public function setValues ( $concept, $total ) {

		$this->concept = $concept;
		$this->total = $total;
	}

	public function getConcept() {
		return $this->concept;
	}

	public function getTotal() {

		return $total;
	}

	public function setTotal( $total ) {
		
		$this->total = $total;
	}
}