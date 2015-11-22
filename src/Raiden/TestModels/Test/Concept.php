<?php

namespace Raiden\TestModels\Test;

/**
 * @table concept
 */
class Concept {

	/**
	 * @PK 
	 * @field id @auto
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