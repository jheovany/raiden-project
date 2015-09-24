<?php

namespace Raiden\TestModels;

/**
 * @table invoice
 */
class Invoice {

	/** 
	 * @field id
	 * @PK
	 */
	private $id;

	/**
	 * @field id_client
	 * @hasone Raiden\TestModels\Client
	 */
	private $client;

	/**
	 * @hasmany Raiden\TestModels\InvoiceDetails
	 * @FK id_invoice
	 */
	private $details;

	public function setValues ( $client, $details ) {

		$this->client = $client;
		$this->details = $details;
	}

	public function getId(){

		return $this->id;
	}

	public function getClient()	{

		return $this->client;
	}

	public function  getTotal() {

		return $this->total;
	}

	public function  getDetails() {

		return $this->invoiceDetails;
	}

}