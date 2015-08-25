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
	 * @field name
	 * @constraint ["not null"]
	 */
	private $name;
}