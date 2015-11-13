<?php

namespace Raiden\TestModels\InvoicingModels;

/**
 * @table invoice_details
 */
class InvoiceDetails {

	/**
	 * @field id_invoice_dt
	 * @PK
	 */
	private $id;

	/**
	 * @field rate_concept_id
	 * @hasone Raiden\InvoicingModels\Concept
	 */
	private $concept;

	/**
	 * @field unit_price
	 * @type float
	 */
	private $unit_price;

	/**
	 * @field exempt_sales
	 * @type float
	 */
	private $exempt_sales;

	/**
	 * @field recorded_sales
	 * @type float
	 */
	private $recorded_sales;

	public function setValues($unit_price, $exempt_sales, $recorded_sales){
		$this->unit_price = $unit_price;
		$this->exempt_sales = $exempt_sales;
		$this->recorded_sales = $recorded_sales;
	}

	public function getId() {
		return $this->id;
	}	

	public function setConcept($concept){
		$this->concept =  $concept;
	}

	public function getConcept() {
		return $this->concept;
	}

	public function getUnitPrice() {
		return $this->unit_price;
	}

	public function getExempt() {
		return $this->exempt_sales;
	}

	public function getRecorded() {
		return $this->recorded_sales;
	}

}