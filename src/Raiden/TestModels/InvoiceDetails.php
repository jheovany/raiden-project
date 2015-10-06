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
	 * @linked Raiden\TestModels\Invoice
	 */
	private $invoice;

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

	public function getId() {
		return $this->id;
	}	

	public function getConcept() {
		return $this->concept;
	}

	public function getTotal() {

		return $this->total;
	}

	public function setTotal( $total ) {
		
		$this->total = $total;
	}
}