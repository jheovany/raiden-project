<?php

namespace Raiden\TestModels;

/**
 * @table invoice
 */
class Invoice {

	/** 
	 * @number[10]+PK id
	 * @ociseq invoice_seq
	 */
	private $id;

	/**
	 * @varchar[12] code
	 * 
	 */
	private $code;

	/**
	 * @FK id_client::Raiden\TestModels\Client
	 */
	private $client;

	/**
	 * @REF invoice_id::Raiden\TestModels\InvoiceDetails
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