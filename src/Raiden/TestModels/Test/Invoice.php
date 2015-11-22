<?php

namespace Raiden\TestModels\Test;

/**
 * @table invoice
 */
class Invoice {

	/** 
	 * @PK @field id 
	 * @ociseq invoice_seq 
	 * @auto
	 */
	private $id;

	/**
	 * @field code
	 * 
	 */
	private $code;

	/**
	 * @field id_client
	 * @belongsTo Raiden\TestModels\Test\Client
	 */
	private $client;

	/**
	 * @hasMany Raiden\TestModels\Test\InvoiceDetails
	 * @FK id_invoice
	 */
	private $details = [];

	public function setValues ( $client, $details ) {

		$this->client = $client;
		$this->details = $details;
	}

	public function getId(){

		return $this->id;
	}

	public function getCode(){

		return $this->code;
	}	

	public function getClient()	{

		return $this->client;
	}

	public function setClient( $client ) {

		$this->client = $client;
	}

	public function setCode($code){
		$this->code = $code;
	}

	public function  getTotal() {

		return $this->total;
	}

	public function  getDetails() {

		return $this->details;
	}

	public function addDetail($det) {
		$this->details[] = $det;
	}
}