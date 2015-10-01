<?php

namespace Raiden\TestModels;

/**
 * @table concept
 */
class Concept {

	/** 
	 * @field id
	 * @PK
	 */
	private $id;

	/**
	 * @field description
	 *
	 *
	 */
	private $description;

	public function getDescription(){

		return $this->description;
	}
}