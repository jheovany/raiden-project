<?php

namespace Raiden;

use Raiden\SQLBuilder\SelectStatement;

class Engine {

	private $reflectionClass;

	private $selectStatement;

	public function setModel( $model ) {

		$this->reflectionClass = new \ReflectionClass($model);

		$properties = $this->reflectionClass->getProperties();

		var_dump($this->reflectionClass->getName());

		

		$selectStatement = new SelectStatement;
		$selectStatement->setTable($this->reflectionClass->getName());
		
		foreach ($properties as $property) {
    		 var_dump($property->getName());
    		 $selectStatement->addColumn( $property->getName() );
		}

		var_dump($selectStatement);
	}
}