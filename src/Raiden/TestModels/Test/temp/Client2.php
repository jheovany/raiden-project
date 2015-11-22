<?php

namespace Raiden\TestModels;

/**
 * @table client
 */
class Client implements \JsonSerializable {

	/**
	 * @PK id
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
	 * @belongsToMany id_client::Raiden\TestModels\Invoice
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