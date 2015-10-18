<?php

namespace Raiden\TestModels;

/**
 * @table concept
 */
class Concept {

	/**
	 * @PK 
	 * @field id
	 */
	private $id;

	/**
	 * @field description
	 */
	private $description;

	public function getDescription(){

		return $this->description;
	}
}