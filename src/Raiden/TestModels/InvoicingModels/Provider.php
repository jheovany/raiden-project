<?php

namespace Raiden\TestModels\InvoicingModels;

/**
 * @table provider
 */
class Provider {

	/** 
	 * @field id_provider @PK @auto
	 * 
	 */
	private $id;

	/** 
	 * @field name
	 * @constraint ["not null"]
	 */
	private $name;

	/** 
	 * @field address
	 * @constraint ["not null"]
	 */
	private $address;

	/** 
	 * @field telephone
	 * @constraint ["not null"]
	 */
	private $telephone;

	/** 
	 * @field email
	 * @constraint ["null"]
	 */
	private $email;

	/** 
	 * @field nrc
	 * @constraint ["not null"]
	 */
	private $nrc;

	/** 
	 * @field nit
	 * @constraint ["not null"]
	 */
	private $nit;

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

	public function setAddress($address){
		$this->address = $address;
	}

	public function getTelephone(){
		return $this->telephone;
	}

	public function setTelephone($telephone){
		$this->telephone = $telephone;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getNRC(){
		return $this->nrc;
	}

	public function setNRC($nrc){
		$this->nrc = $nrc;
	}

	public function getNIT(){
		return $this->nit;
	}

	public function setNIT($nit){
		$this->nit = $nit;
	}

	public function getSubs(){
		return $this->sub_stations;
	}
}