<?php

namespace Raiden;

use Raiden\SQLBuilder\SelectStatement;
use Raiden\SQLBuilder\InsertStatement;
use Raiden\SQLBuilder\UpdateStatement;
use DocBlockReader\Reader;

class Engine {

	private $modelClass;

	private $reflectionClass;

	private $metaObject;

	private $fetchedObjects = [];

	private $fetchedRows = [];

	private $connect;

	private $isJson = false;

	private $filter;

	function __construct ( ) {
		
		$a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i)) { 
            call_user_func_array(array($this,$f),$a);
        } 
	}

	function __construct1 ( $modelClass ) {

		$this->initialize ( $modelClass );
	}

	public function initialize ( $modelClass, $parentEngine = null) {

		$this->connect = (new DBConnector)->connector();

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

			if (array_key_exists( 'belongsto', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['belongsto'] = $parameters->getParameter('belongsto');
			}

			if (array_key_exists( 'PK', $parameters->getParameters() )) {
				$this->metaObject['PK'] = $parameters->getParameter('field');
				$this->metaObject['property-pk'] = $propertyName;
				$this->metaObject['properties'][$propertyName]['PK'] = $parameters->getParameter('PK');
			}			

    		if (array_key_exists( 'hasone', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['hasone'] = $parameters->getParameter('hasone');
				
				$engine = new Engine();
				$engine->initialize( new $this->metaObject['properties'][$propertyName]['hasone'] , $this);		
    			
    			$this->metaObject['properties'][$propertyName]['metaobject'] = $engine->getMetaObject();
    			$this->metaObject['properties'][$propertyName]['engine'] = $engine;
    		}
    		
    		if (array_key_exists( 'hasmany', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['hasmany'] = $parameters->getParameter('hasmany');
				$this->metaObject['properties'][$propertyName]['FK'] = $parameters->getParameter('FK');    			

				$engine = new Engine();
    			$engine->initialize( new $this->metaObject['properties'][$propertyName]['hasmany'] , $this);

    			$this->metaObject['properties'][$propertyName]['metaobject'] = $engine->getMetaObject();
    			$this->metaObject['properties'][$propertyName]['engine'] = $engine;
			}
		}
		//var_dump($this->metaObject);
	} 

	public function getMetaObject () { return $this->metaObject; }

	public function getReflectionClass () { return $this->reflectionClass; }

	public function getFilter ( ) {

		$this->filter = new Filter;
		$this->filter->initialize($this);

		return $this->filter;
	}

	public function findByPK( $id ) {

		return $this->findWhere($id);
	}	

	public function findByFK($cond) {
		
		return $this->findWhere($cond, true);
	}

	public function find() {
		
		if ($this->filter) {
			return $this->findWhere($this->filter->getSqlFilter(), true);
		} else {
			return $this->findAll();
		}
	}

	public function findAll($limit = 100) {

		return $this->findWhere($limit, false, true);
	}	

	public function findWhere( $cond, $isWhere = false, $isLimit = false ) {

		$this->fetchedObjects = null;

		$select = new SelectStatement;
		$select->setTable($this->metaObject['tablename']);

		//dd($this->metaObject['properties']);

		foreach ($this->metaObject['properties'] as $property) {
			
			if (array_key_exists( 'fieldname', $property )) {
				$column = $property['fieldname'];
				$select->addColumn($column);
			}
		}

		if ($isWhere) {
			$select->setCondition("WHERE " . $cond);
		} else if ($isLimit) {
			$select->setCondition("LIMIT " . $cond);
		} else {
			$pkey = $this->metaObject['PK'];
			$select->setCondition("WHERE $pkey = " . $cond);
		}

		$db = $this->connect;
		$queryString = $select->getSelect();
		//var_dump( $queryString );

		$statement = $db->prepare( $queryString );
		$statement->execute();

		$rows = $statement->fetchAll(); 

		$this->fetchedRows = $rows;

		//echo 'class: ' . $this->metaObject['classname'] . '<br>';
		//echo 'table: ' . $this->metaObject['tablename'];
		//var_dump($rows);

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
				}

				if (array_key_exists( 'hasone', $property )) {	

					$fk = $row[$property['fieldname']];

					$engine = $property['engine'];

 					$hasOneObject = $engine->findByPK( $fk );

 					$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
					$reflectionProperty->setAccessible(true);
					$reflectionProperty->setValue( $object, $hasOneObject);
				}
				
				if (array_key_exists( 'hasmany', $property )) {

					$pk = $this->metaObject['PK'];
					$fk = $property['FK'];

					$value = $row[$pk];

					$engine = $property['engine'];

 					$hasManyObjects	= $engine->findByFK("$fk = $value");

 					$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
					$reflectionProperty->setAccessible(true);
					$reflectionProperty->setValue( $object, $hasManyObjects);
				}
			}
			
			$this->fetchedObjects[] = $object; 	
		}

		//echo 'fetched objects:';
		//var_dump($this->fetchedObjects);

		if (!$isWhere and !$isLimit) {
			$this->modelClass = $this->fetchedObjects[0];
			return $this->fetchedObjects[0];
		} else {
			return $this->fetchedObjects;
		}
	}

	public function save(){

		return $this->privateSave();
	}

	private function privateSave( $addVals = null ) {

		$values = [];

		if (!is_null($addVals)) {
			$values[key($addVals)] = $addVals[key($addVals)];	
		}

		//echo 'valores adicionales :';
		//var_dump($values);

		$insert = new InsertStatement;

		$insert->setTable($this->metaObject['tablename']);

		foreach ($this->metaObject['properties'] as $property) {

			if (array_key_exists( 'fieldname', $property ) and 
				!array_key_exists( 'PK', $property ) and
				!array_key_exists( 'hasone', $property ) and
				!array_key_exists( 'belongsto', $property ) ) {

				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);
				
				$value = $reflectionProperty->getValue( $this->modelClass );

				if (is_string($value)) {
					$value = "'$value'";
				}

				$values[ $property['fieldname'] ] = $value;
			}

			if ( array_key_exists( 'hasone', $property ) ) {
				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);

				$object = $reflectionProperty->getValue( $this->modelClass );

				//echo 'has one :';
				//var_dump($object);

				$objectEngine = new Engine( $object );

				$ppk = $objectEngine->getMetaObject()['property-pk'];

				$refClass = $objectEngine->getReflectionClass();

				$reflectionProperty2 = $refClass->getProperty( $ppk );
				$reflectionProperty2->setAccessible(true);

				$values[ $property['fieldname'] ] = $reflectionProperty2->getValue( $object ); 
			}

			echo 'valores adicionales: ';
			var_dump($values);
		}

		$sql = $insert->getInsert($values);

		$db = $this->connect;
		$queryString = $sql;
		//var_dump( $queryString );

		$statement = $db->prepare( $queryString );
		//$statement->execute();

		if	( !$statement->execute() ) {
			var_dump( $statement->errorInfo() );
			return;
		}

		$lastId = $db->lastInsertId(); 

		echo 'last id: ';
		var_dump($lastId);

		foreach ($this->metaObject['properties'] as $property) {
			if ( array_key_exists ( 'hasmany', $property ) ) {
				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);

				$objects = $reflectionProperty->getValue( $this->modelClass );

				$value = [];

				$value[$property['FK']] = $lastId;

				

				foreach ($objects as $obj) {
					$engine = new Engine($obj);

					$reflexionMethod = new \ReflectionMethod($engine, 'privateSave');
					$reflexionMethod->setAccessible(true);
					$reflexionMethod->invoke($engine, $value);
				}
			}
		}

		return $this->findByPK($lastId);
	}

	public function getModel() {

		return $this->modelClass;
	}

	public function modify() {

		$update = new UpdateStatement;
		$update->setTable($this->metaObject['tablename']);

		foreach ($this->metaObject['properties'] as $property) {

			if (array_key_exists( 'fieldname', $property ) and 
				!array_key_exists( 'PK', $property ) and
				!array_key_exists( 'hasone', $property ) ) {

				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);
				
				$value = $reflectionProperty->getValue( $this->modelClass );

				if (is_string($value)) {
					$value = "'$value'";
				}

				$values[ $property['fieldname'] ] = $value;
			}

			if ( array_key_exists( 'hasone', $property ) ) {
				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);

				$object = $reflectionProperty->getValue( $this->modelClass );

				$objectEngine = new Engine( $object );

				$ppk = $objectEngine->getMetaObject()['property-pk'];

				$refClass = $objectEngine->getReflectionClass();

				$reflectionProperty2 = $refClass->getProperty( $ppk );
				$reflectionProperty2->setAccessible(true);

				$values[ $property['fieldname'] ] = $reflectionProperty2->getValue( $object ); 
			}
		}

		$ppk = $this->metaObject['property-pk'];

		$reflectionProperty = $this->reflectionClass->getProperty( $ppk );
		$reflectionProperty->setAccessible(true);

		$pk = $this->metaObject['PK'];
		$pkVal = $reflectionProperty->getValue( $this->modelClass );

		$cond = $pk.' = '.$pkVal;

		$sql = $update->getUpdate($values, $cond);

		$db = $this->connect;
		$queryString = $sql;

		$statement = $db->prepare( $queryString );

		if	( $statement->execute() ) {
			$this->modelClass = $this->findByPK($pkVal);
			return true;
		} else {
			var_dump( $statement->errorInfo() );
			return false;
		}
	}

	public function destroy() {

		$table = $this->metaObject['tablename'];

		$ppk = $this->metaObject['property-pk'];

		$reflectionProperty = $this->reflectionClass->getProperty( $ppk );
		$reflectionProperty->setAccessible(true);

		$pk = $this->metaObject['PK'];
		$pkVal = $reflectionProperty->getValue( $this->modelClass );

		$sql = "DELETE FROM $table WHERE $pk = $pkVal";

		$this->modelClass = null;

		$db = $this->connect;
		$queryString = $sql;

		$statement = $db->prepare( $queryString );

		if	( $statement->execute() ) {
			return true;
		} else {
			var_dump( $statement->errorInfo() );
			return false;
		}
	}

	public function toJson ( $convert = true ) {


	}

	public function fetch( $property ) {

		if (array_key_exists($property, $this->metaObject['properties']) and 
			array_key_exists('hasmany',$this->metaObject['properties'][$property])) {

			$cont = 0;

			foreach ($this->fetchedObjects as $key => $object) {
				
				$reflectionProperty = $this->reflectionClass->getProperty( $this->metaObject['property-pk'] );
				$reflectionProperty->setAccessible(true);
				$fkval = $reflectionProperty->getValue( $object );

				$fk = $this->metaObject['properties'][$property]['FK'];

				$engine = $this->metaObject['properties'][$property]['engine'];
				
				$hasManyObjects	= $engine->findByFK("$fk = $fkval");

				//var_dump($hasManyObjects);

				$reflectionProperty2 = $this->reflectionClass->getProperty( $property );
				$reflectionProperty2->setAccessible(true);
				$reflectionProperty2->setValue( $object, $hasManyObjects);
			}
		}

		else {
			
			throw new \Exception(
				"La propiedad '$property' no representa a una coleccion de objetos objeto");
		}
	}
}

