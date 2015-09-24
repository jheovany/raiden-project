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
	private $firstName;

	/**
	 * @field last_name
	 * @constraint ["not null"]
	 */
	private $lastName;

	/**
	 * @field dui
	 * @constraint ["not null"]
	 */
	private $Dui;

	/**
	 * @field nit
	 * @constraint ["not null"]
	 */
	private $Nit;

	public function getFirtsName() {

		return $this->firtsName;
	}
}