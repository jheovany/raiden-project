<?php

namespace Raiden\TestModels\InvoicingModels;

/**
 * @table client
 */
class Client {

	/**
	 * @field id_client @PK @auto
	 * 
	 */
	private $id;

	/**
	 * @field name
	 * @type varchar
	 */
	private $name;

	/**
	 * @field address
	 * @constraint ["not null"]
	 */
	private $address;

	/**
	 * @field account_num
	 * @constraint ["not null"]
	 */
	private $account_num;

	/**
	 * @field sub_id
	 * @belongsTo Raiden\TestModels\InvoicingModels\SubStation
	 */
	private $subStation;

	/**
	 * @hasMany Raiden\TestModels\InvoicingModels\Invoice 
	 * @FK client_id_client
	 */
	public $invoices;

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getAddress(){
		return $this->address;
	}

	public function setAddress($adress){
		$this->address = $adress;
	}

	public function getAccountNum(){
		return $this->account_num;
	}

	public function setAccountNum($account_num){
		$this->account_num = $account_num;
	}

	public function getSubStation(){
		return $this->subStation;
	}

	public function setSubStation($subStation){
		$this->subStation = $subStation;
	}
}