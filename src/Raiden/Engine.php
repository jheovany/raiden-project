<?php

namespace Raiden;

use Raiden\SQLBuilder\SelectStatement;
use DocBlockReader\Reader;

class Engine {

	private $modelClass;

	private $reflectionClass;

	private $selectStatement;

	private $metaObject = [];

	private $fetchedObjects = [];

	private $dbConnect;

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

			$this->metaObject['properties'][$propertyName]['property'] = $propertyName;

			if (array_key_exists( 'field', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['fieldname'] = $parameters->getParameter('field');
			}

			if (array_key_exists( 'PK', $parameters->getParameters() )) {
				$this->metaObject['PK'] = $parameters->getParameter('field');
			}			

    		if (array_key_exists( 'hasone', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['hasone'] = $parameters->getParameter('hasone');
    			
    			$engine = new Engine();
    			$engine->initialize( new $this->metaObject['properties'][$propertyName]['hasone']);
    			$this->metaObject['properties'][$propertyName]['metaobject'] = $engine->getMetaObject();
    			$this->metaObject['properties'][$propertyName]['engine'] = $engine;
    		}

    		if (array_key_exists( 'hasmany', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['hasmany'] = $parameters->getParameter('hasmany');
				$this->metaObject['properties'][$propertyName]['FK'] = $parameters->getParameter('FK');    			
			
				$engine = new Engine();
    			$engine->initialize( new $this->metaObject['properties'][$propertyName]['hasmany']);
    			$this->metaObject['properties'][$propertyName]['metaobject'] = $engine->getMetaObject();
    			$this->metaObject['properties'][$propertyName]['engine'] = $engine;
			}
		}

		//var_dump($this->metaObject);
	} 

	public function getMetaObject () { return $this->metaObject; }

	public function find( $id, $field = null ) {

		$select = new SelectStatement;
		$select->setTable($this->metaObject['tablename']);

		foreach ($this->metaObject['properties'] as $property) {
			
			if (array_key_exists( 'fieldname', $property )) {
				$column = $property['fieldname'];
				$select->addColumn($column);
			}
		}

		if ( !$field ) {
			$field = $this->metaObject['PK'];
		}

		$select->setCondition("WHERE $field = " . $id);

		$db = (new Connect)->getConnection();
		$queryString = $select->getSelect();
		var_dump( $queryString );

		$statement = $db->prepare( $queryString );
		$statement->execute();

		$rows = $statement->fetchAll();

		foreach ($rows as $row) {
				
			$className = $this->reflectionClass->getName();

			$object = new $className;

			foreach ($this->metaObject['properties'] as $property) {

				if (array_key_exists( 'fieldname', $property ) and !array_key_exists( 'hasone', $property )) {	

					//var_dump($property['property']);

					$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
					$reflectionProperty->setAccessible(true);
					$reflectionProperty->setValue( $object, $row[$property['fieldname']]);
					$fk = $reflectionProperty->getValue( $object );
					//var_dump($fk);
				}

				if (array_key_exists( 'hasone', $property )) {	

					$fk = $row[$property['fieldname']];

					$engine = $property['engine'];

 					$hasOneObject	= $engine->find( $fk );

 					$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
					$reflectionProperty->setAccessible(true);
					$reflectionProperty->setValue( $object, $hasOneObject[0]);
				}

				if (array_key_exists( 'hasmany', $property )) {

					$pk = $this->metaObject['PK'];
					$fk = $property['FK'];

					$value = $row[$pk];

					$engine = $property['engine'];

 					$hasManyObjects	= $engine->find($value, $fk);

 					$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
					$reflectionProperty->setAccessible(true);
					$reflectionProperty->setValue( $object, $hasManyObjects);
				}
			}
			
			$this->fetchedObjects[] = $object; 	
		}

		return $this->fetchedObjects;
	}

	public function getSelectStatement() {

		return $selectStatement;
	}
}