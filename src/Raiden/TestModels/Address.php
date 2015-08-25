<?php

namespace Raiden\TestModels;


/**
 * @table address
 */
class Address {

	/**
	 * @field id 
	 * @PK
	 */
	private $id;

	/**
	 * @field idClient
	 */
	private $client;

	/**
	 * @field accountNumber 
	 * @constraint ["not null"]
	 */
	private $accountNumber;

}