<?php

namespace Raiden\TestModels\Test;

/**
 * @table invoice_details
 */
class InvoiceDetails {

	/**
	 * @field id
	 * @PK @auto
	 */
	private $id;

	/**
	 * @field id_invoice
	 * @belongsTo Raiden\TestModels\Test\Invoice
	 */
	private $invoice;

	/**
	 * @field id_concept
	 * @belongsTo Raiden\TestModels\Test\Concept
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