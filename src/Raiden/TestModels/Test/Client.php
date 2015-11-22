<?php

namespace Raiden\TestModels\Test;

/**
 * @table client
 */
class Client {

	/**
	 * @PK @field id
	 * @auto
	 */
	private $id;

	/**
	 * @field first_name
	 */
	private $firstname;

	/**
	 * @field last_name
	 */
	private $lastname;

	/**
	 * @field dui
	 */
	private $dui;

	/**
	 * @field nit
	 */
	private $nit;

	/**
	 * @FK id_client @hasMany Raiden\TestModels\Test\Invoice
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
}