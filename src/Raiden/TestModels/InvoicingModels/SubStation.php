<?php

namespace Raiden\TestModels\InvoicingModels;

/**
 * @table sub_station
 */
class SubStation {

	/** 
	 * @field id_sub @PK @auto
	 *
	 */
	private $id;

	/**
	 * @field place_name
	 * @constraint ["not null"]
	 */
	private $place_name;

	/**
	 * @field address
	 * @constraint ["not null"]
	 */
	private $address;

	/**
	 * @field zone_id
	 * @belongsTo Raiden\TestModels\InvoicingModels\Zone
	 */
	private $zone;

	/**
	 * @hasMany Raiden\TestModels\InvoicingModels\Client
	 * @FK sub_id
	 */
	private $clients;
	
	public function getId(){
		return $this->id;
	}

	public function getPlaceName(){
		return $this->place_name;
	}

	public function setPlaceName($place_name){
		$this->place_name = $place_name;
	}

	public function getAddress(){
		return $this->address;
	}

	public function setAddress($address){
		$this->address = $address;
	}

	public function getZone(){
		return $this->zone;
	}

	public function setZone($zone){
		$this->zone = $zone;
	}

	public function getClients(){
		return $this->clients;
	}
}