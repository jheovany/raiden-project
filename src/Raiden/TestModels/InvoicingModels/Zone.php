<?php

namespace Raiden\TestModels\InvoicingModels;

/**
 * @table zone
 */
class Zone {

	/** 
	 * @field id_zone @PK @auto
	 * 
	 */
	private $id;

	/**
	 * @field zone_name
	 * @constraint ["not null"]
	 */
	private $name;

	/**
	 *@hasMany Raiden\TestModels\InvoicingModels\SubStation
	 *@FK zone_id
	 */
	private $sub_stations; 

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getSubs(){
		return $this->sub_stations;
	}
}