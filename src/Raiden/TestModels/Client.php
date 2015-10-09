<?php

namespace Raiden\TestModels;

/**
 * @table client
 */
class Client implements \JsonSerializable {

	/**
	 * @field id
	 * @PK
	 */
	private $id;

	/**
	 * @field first_name
	 * @constraint ["not null"]
	 */
	private $firstname;

	/**
	 * @field last_name
	 * @constraint ["not null"]
	 */
	private $lastname;

	/**
	 * @field dui
	 * @constraint ["not null"]
	 */
	private $dui;

	/**
	 * @field nit
	 * @constraint ["not null"]
	 */
	private $nit;

	/**
	 * hasmany Raiden\TestModels\Invoice
	 * FK id_client
	 */
	private $invoices = [];


	public function getFirstname() {

		return $this->firstname;
	}

	public function getLastname() {

		return $this->lastname;
	}

	public function getDui() {

		return $this->dui;
	}

	public function getNit() {

		return $this->nit;
	}

	public function JsonSerialize()
	{
		$vars = get_object_vars($this);

		return $vars;
	}
}