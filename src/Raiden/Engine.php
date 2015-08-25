<?php

namespace Raiden;

use Raiden\SQLBuilder\SelectStatement;
use DocBlockReader\Reader;

class Engine {

	private $modelClass;

	private $reflectionClass;

	private $oneObjects = [];

	private $selectStatement;

	private $metaObject = [];

	public function initialize( $modelClass ) {

		$this->modelClass = $modelClass;

		$this->reflectionClass = new \ReflectionClass ( $this->modelClass );

		$readerClass = new Reader( $this->reflectionClass->getName() );

		/****/

		$className = $this->reflectionClass->getName();
		$tableName = $readerClass->getParameter( "table" );

		$this->metaObject['class'] = $this->modelClass;
		$this->metaObject['classname'] = $className;
		$this->metaObject['tablename'] = $tableName;

		$properties = $this->reflectionClass->getProperties();

		foreach ($properties as $property) {

			$propertyName = $property->getName();

			$parameters = new Reader($className, (string) $propertyName, 'property' );

			$this->metaObject['properties'][$propertyName]['fieldname'] = $parameters->getParameter('field');

    		if (array_key_exists( 'hasone', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['hasone'] = $parameters->getParameter('hasone');    			
    		}

    		if (array_key_exists( 'hasmany', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['hasmany'] = $parameters->getParameter('hasmany');    			
			}
		}

		var_dump($this->metaObject);
	} 

	public function populate () {

		$objectList = [];

		return $objectList;
	}

	private function mapOneObject() {

		$properties = $this->reflectionClass->getProperties();

		foreach ($properties as $property) {

    		$aps = new Reader($this->reflectionClass->getName(), $property->getName(), 'property' );
    			
    		if ( array_key_exists( 'hasOne', $aps->getParameters()) ) {
    			var_dump( $aps->getParameter( 'hasOne' ) );

    			$oneEngine = new Engine;
    			$oneEngine->initialize( $aps->getParameter( 'hasOne' ) );

    		}
		}

		return $this->modelClass; 
	}

	public function getSelectStatement() {

		return $selectStatement;
	}
}