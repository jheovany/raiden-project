<?php

namespace Raiden\TestModels;

/**
 * @table invoice_details
 */
class InvoiceDetails {

	/**
	 * @PK id
	 */
	private $id;

	/**
	 * @belongsto id_invoice::Raiden\TestModels\Invoice
	 */
	private $invoice;

	/**
	 * @hasone id_concept::Raiden\TestModels\Concept
	 */
	private $concept;

	/**
	 * @number[10,2] total
	 * @var double
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