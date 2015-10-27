<?php

namespace Raiden\TestModels;

/**
 * @table invoice
 */
class Invoice implements \JsonSerializable {

	/** 
	 * @PK @field id @ociseq invoice_seq
	 */
	private $id;

	/**
	 * @field code
	 * 
	 */
	private $code;

	/**
	 * @field id_client
	 * @hasone Raiden\TestModels\Client
	 */
	private $client;

	/**
	 * @hasmany Raiden\TestModels\InvoiceDetails
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

	public function JsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}