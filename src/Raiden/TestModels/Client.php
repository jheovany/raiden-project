<?php

namespace Raiden\TestModels;


/**
 * @table client
 */
class Client {

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

	public function getFirstname() {

		return $this->firstname;
	}

	public function getLastname() {

		return $this->lastname;
	}
}