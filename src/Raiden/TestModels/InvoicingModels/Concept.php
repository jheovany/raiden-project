<?php

namespace Raiden\TestModels\InvoicingModels;

/**
 * @table rate_concepts
 */
class Concept {

	/** 
	 * @field id_rt_cpt
	 * @PK
	 */
	private $id;

	/**
	 * @field concept
	 * @constraint ["not null"]
	 */
	private $concept;

	public function getConcept(){
		return $this->concept;
	}

	public function setConcept($concept){
		$this->concept = $concept;
	}
	
}