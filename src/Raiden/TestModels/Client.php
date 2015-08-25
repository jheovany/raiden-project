<?php

namespace Raiden\TestModels;


/**
 * @table Client
 */
class Client {

	/**
	 * @field id 
	 * @constraint ["primary key", "not null"]
	 */
	private $id;

	/**
	 * @field name
	 * @constraint ["not null"]
	 */
	private $name;

}