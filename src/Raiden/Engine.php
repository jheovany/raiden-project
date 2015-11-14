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

	private $isMaster = true;

	private $parentEngines = []; 

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

	public function initialize ( $modelClass, $master = true) {

		$this->isMaster = $master;

		/*echo 'master: ';
		var_dump($this->isMaster);*/

		$this->connect = (new DBConnector)->connector();

		$this->modelClass = $modelClass;

		$this->reflectionClass = new \ReflectionClass ( $this->modelClass );

		$readerClass = new Reader( $this->reflectionClass->getName() );

		/****/

		$className = $this->reflectionClass->getName();
		$tableName = $readerClass->getParameter( "table" );

		$this->metaObject['class'] = $this->modelClass;
		$this->metaObject['className'] = $className;
		$this->metaObject['tableName'] = $tableName;

		$properties = $this->reflectionClass->getProperties();

		foreach ($properties as $property) {

			$propertyName = $property->getName();

			$parameters = new Reader($className, (string) $propertyName, 'property' );

			$this->metaObject['properties'][$propertyName]['property'] = $propertyName;

			if (array_key_exists( 'field', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['fieldName'] = $parameters->getParameter('field');
			}

			if (array_key_exists( 'auto', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['auto'] = $parameters->getParameter('auto');
			}
			
			if (array_key_exists( 'hasOne', $parameters->getParameters() )) {

				$this->metaObject['properties'][$propertyName]['hasOne'] = $parameters->getParameter('hasOne');

				$engine = new Engine();
				$engine->initialize( new $this->metaObject['properties'][$propertyName]['hasOne'] , false);		
    			
    			$this->metaObject['properties'][$propertyName]['metaObject'] = $engine->getMetaObject();
    			$this->metaObject['properties'][$propertyName]['engine'] = $engine;
			}
			
			if (array_key_exists( 'PK', $parameters->getParameters() )) {
				$this->metaObject['PK'] = $parameters->getParameter('field');
				$this->metaObject['propertyPK'] = $propertyName;
				$this->metaObject['properties'][$propertyName]['PK'] = $parameters->getParameter('PK');
			}

			if (array_key_exists( 'ociseq', $parameters->getParameters() )) {
				$this->metaObject['properties'][$propertyName]['ociseq'] = $parameters->getParameter('ociseq');
			}			

    		if (array_key_exists( 'belongsTo', $parameters->getParameters() )) {
				
				$this->metaObject['properties'][$propertyName]['belongsTo'] = $parameters->getParameter('belongsTo');

				$c = $this->metaObject['properties'][$propertyName]['belongsTo'];
				
				if (array_key_exists($c, $this->parentEngines)) {

					$engine = $this->parentEngines[$c];
				}

				else {

					$engine = new Engine();
					$engine->addParentEngine( $this->metaObject['className'], $this);
					$engine->initialize( new $c, false);		
				}

    			$this->metaObject['properties'][$propertyName]['metaObject'] = $engine->getMetaObject();
    			$this->metaObject['properties'][$propertyName]['engine'] = $engine;
    		}
    		
    		if (array_key_exists( 'hasMany', $parameters->getParameters() )) {

				$this->metaObject['properties'][$propertyName]['hasMany'] = $parameters->getParameter('hasMany');
				$this->metaObject['properties'][$propertyName]['FK'] = $parameters->getParameter('FK');

				$c = $this->metaObject['properties'][$propertyName]['hasMany'];    			

				if (array_key_exists($c, $this->parentEngines)) {

					$engine = $this->parentEngines[$c];
				}

				else {

					$engine = new Engine();
					$engine->addParentEngine( $this->metaObject['className'], $this);
					$engine->initialize( new $c , false);
				}

    			$this->metaObject['properties'][$propertyName]['metaObject'] = $engine->getMetaObject();
    			$this->metaObject['properties'][$propertyName]['engine'] = $engine;
			}

			if (array_key_exists( 'through', $parameters->getParameters() )) {
				
				$this->metaObject['properties'][$propertyName]['through'] = $parameters->getParameter('through');
			}
		}

		//echo 'metaObject';
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

		$db = $this->connect;
		$driverName = $db->getAttribute(\PDO::ATTR_DRIVER_NAME);

		$this->fetchedObjects = null;

		$select = new SelectStatement;
		$select->setTable($this->metaObject['tableName']);

		//dd($this->metaObject['properties']);

		foreach ($this->metaObject['properties'] as $property) {
			
			if (array_key_exists( 'fieldName', $property )) {
				$column = $property['fieldName'];
				$select->addColumn($column);
			}
		}

		if ($isWhere) {
			$select->setCondition("WHERE " . $cond);
		} else if ($isLimit) {

			if ( $driverName == 'oci' ) {

				$select->setCondition("WHERE ROWNUM <= " . $cond);				
			}

			else {

				$select->setCondition("LIMIT " . $cond);

			}

		} else {
			$pkey = $this->metaObject['PK'];
			$select->setCondition("WHERE $pkey = " . $cond);
		}

		
		$queryString = $select->getSelect();
		
		/*echo 'SQL command: ';
		var_dump( $queryString );*/

		$statement = $db->prepare( $queryString );
		//$statement->execute();

		if	( !$statement->execute() ) {
			var_dump( $statement->errorInfo() );
			return;
		}

		$rows = $statement->fetchAll(); 

		$this->fetchedRows = $rows;

		//var_dump($rows);

		//return;

		$className = $this->metaObject['className'];

		foreach ($rows as $row) {
				
			$object = new $className;

			foreach ($this->metaObject['properties'] as $property) {

				if (array_key_exists( 'fieldName', $property ) and 
					!array_key_exists( 'hasOne', $property ) and
					!array_key_exists( 'belongsTo', $property )) {

					if ( $driverName == 'oci' ) {

						$value = $row[strtoupper($property['fieldName'])];

					} else {

						$value = $row[$property['fieldName']];
					}

					$rc = new \ReflectionClass( $object );
					
					$reflectionProperty = $rc->getProperty( $property['property'] );
					$reflectionProperty->setAccessible(true);
					$reflectionProperty->setValue( $object, $value);
				}
				
				if (array_key_exists( 'belongsTo', $property )) {

					$c = $property['belongsTo'];

					if (!array_key_exists($c, $this->parentEngines)) {

						if ( $driverName == 'oci' ) {

							$fk = $row[strtoupper($property['fieldName'])];

						} else {

							$fk = $row[$property['fieldName']];
						}

						$engine = $property['engine'];
						/*
						echo 'Engine';
						var_dump($engine);

						return;*/

						$hasOneObject = $engine->findByPK( $fk );

	 					$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
						$reflectionProperty->setAccessible(true);
						$reflectionProperty->setValue( $object, $hasOneObject);
					}
				}

				if (array_key_exists( 'hasMany', $property ) and 
					!array_key_exists( 'through', $property )) {

					$c = $property['hasMany'];
				
					if (!array_key_exists($c, $this->parentEngines)) {

						$pk = $this->metaObject['PK'];
						$fk = $property['FK'];

						if ($driverName == 'oci') {

							$value = $row[strtoupper($pk)];
						}

						else {

							$value = $row[$pk];
						}
					
						$engine = $property['engine'];
						$hasManyObjects	= $engine->findByFK("$fk = $value");

 						$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
						$reflectionProperty->setAccessible(true);
						$reflectionProperty->setValue( $object, $hasManyObjects);
					}
				}
				/* Relacion muchos a muchos */
				if (array_key_exists( 'hasMany', $property ) and 
					array_key_exists( 'through', $property )) {

					$c = $property['hasMany'];
				
					if (!array_key_exists($c, $this->parentEngines)) {

						$pk = $this->metaObject['PK'];

						$interTable_rightTableFK = explode(".", $property['through']);

						$interTable = (string) array_shift($interTable_rightTableFK);
						
						$leftTableFK = $property['FK'];
						
						$rightTableFK = (string) array_shift($interTable_rightTableFK);

						if ($driverName == 'oci') {

							$value = $row[strtoupper($pk)];	
						}

						else {

							$value = $row[$pk];	
						}

						$engine = $property['engine'];

						$rightTablePK = $engine->getMetaObject()['PK'];

						$sql = "$rightTablePK IN ( SELECT $rightTableFK FROM $interTable WHERE $leftTableFK = $value )";

	 					$hasManyObjects	= $engine->findWhere($sql, true);

	 					$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
						$reflectionProperty->setAccessible(true);
						$reflectionProperty->setValue( $object, $hasManyObjects);

					}
				}
			}
			
			$this->fetchedObjects[] = $object; 	
		}

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

		$db = $this->connect;

		$values = [];

		if (!is_null($addVals)) {
			$values[key($addVals)] = $addVals[key($addVals)];
		}


		$insert = new InsertStatement;

		$insert->setTable($this->metaObject['tableName']);

		foreach ($this->metaObject['properties'] as $property) {

			if ( array_key_exists( 'ociseq', $property ) ) {

				$seq = $property['ociseq'];

				$seq = "$seq.nextval";
				
				$values[ $this->metaObject['PK'] ] = $seq;

				echo 'seq: ';
				var_dump($seq);
			}

			if (array_key_exists( 'fieldName', $property ) and 
				!array_key_exists( 'auto', $property ) and
				!array_key_exists( 'ociseq', $property ) and
				!array_key_exists( 'hasOne', $property ) and
				!array_key_exists( 'belongsTo', $property ) ) {

				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);
				
				$value = $reflectionProperty->getValue( $this->modelClass );

				if (array_key_exists( 'PK', $property)) {
					$lastId = $value;
				}

				if (is_string($value)) {
					$value = "'$value'";
				}

				$values[ $property['fieldName'] ] = $value;
				
			}

			if ( array_key_exists( 'belongsTo', $property ) and
				!array_key_exists( 'through', $property ) ) {

				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);

				$object = $reflectionProperty->getValue( $this->modelClass );

				$objectEngine = new Engine( $object );

				$ppk = $objectEngine->getMetaObject()['propertyPK'];

				$refClass = $objectEngine->getReflectionClass();

				$reflectionProperty2 = $refClass->getProperty( $ppk );
				$reflectionProperty2->setAccessible(true);

				$values[ $property['fieldName'] ] = $reflectionProperty2->getValue( $object ); 
			}

		}

		$sql = $insert->getInsert($values);

		$queryString = $sql;
		//var_dump( $queryString );

		$statement = $db->prepare( $queryString );
		//$statement->execute();

		if	( !$statement->execute() ) {
			var_dump( $statement->errorInfo() );
			return;
		}

		if ( array_key_exists('auto', $this->metaObject['properties'][$this->metaObject['propertyPK']] ) ) {

			if ( $driverName == 'mysql' ) { 
				
				$lastId = $db->lastInsertId( );
			}

			if ( $driverName == 'pgsql' ) {

				$table = $this->metaObject['tablename'];
				$pk = $this->metaObject['PK'];

				$seq = $table."_".$pk."_seq";

				$lastId = $db->lastInsertId( $seq );
			}

			if ( $driverName == 'oci' ) { 
				
				$table = $this->metaObject['tablename'];
				$pk = $this->metaObject['PK'];

				$queryString = "SELECT MAX($pk) AS id FROM $table";

				$statement = $db->prepare( $queryString );
				//$statement->execute();

				if	( !$statement->execute() ) {
					var_dump( $statement->errorInfo() );
					return;
				}

				$id = $statement->fetch();

				$lastId = $id['ID'];
			}
		}

		if ( array_key_exists('ociseq', $this->metaObject['properties'][$this->metaObject['propertyPK']] ) ) {

			$seq = $this->metaObject['properties'][$this->metaObject['property-pk']]['ociseq'];
			
			$querySeq = "select $seq.currval as last_id from dual";

			$statement = $db->prepare( $querySeq );

			if	( !$statement->execute() ) {
				var_dump( $statement->errorInfo() );
				return;
			}

			$seqVal = $statement->fetch();
			
			$lastId = $seqVal['LAST_ID'];
		}

		foreach ($this->metaObject['properties'] as $property) {
			
			if (array_key_exists ( 'hasMany', $property ) and
				!array_key_exists ( 'through', $property )) {
				
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

			/* Through */
			if ((array_key_exists ( 'hasMany', $property ) and
				array_key_exists ( 'through', $property ))) {
				
				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);

				$objects = $reflectionProperty->getValue( $this->modelClass );

				$pk = $this->metaObject['PK'];

				$interTable_rightTableFK = explode(".", $property['through']);

				$interTable = (string) array_shift($interTable_rightTableFK);
					
				$leftTableFK = $property['FK'];
					
				$rightTableFK = (string) array_shift($interTable_rightTableFK);

				foreach ($objects as $obj) {

					$engine = new Engine($obj);

					$objPK = $engine->getMetaObject()['PK']; 
					
					$reflectionProperty = $this->reflectionClass->getProperty( $objPK );
					$reflectionProperty->setAccessible(true);

					$pkValObj =	$reflectionProperty->getValue( $obj );
					
					$sql = 	"INSERT INTO $interTable ($leftTableFK, $rightTableFK) 
							VALUES ($lastId, $pkValObj )";

					$db = $this->connect;
					$statement = $db->prepare( $sql );

					if	( !$statement->execute() ) {

						var_dump( $statement->errorInfo() );
						return;
					}
				}
			}
		}

		return $this->findByPK($lastId);
	}

	public function modify() {

		$update = new UpdateStatement;
		$update->setTable($this->metaObject['tablename']);

		foreach ($this->metaObject['properties'] as $property) {

			if (array_key_exists( 'fieldName', $property ) and 
				!array_key_exists( 'PK', $property ) and
				!array_key_exists( 'hasOne', $property ) ) {

				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);
				
				$value = $reflectionProperty->getValue( $this->modelClass );

				if (is_string($value)) {
					$value = "'$value'";
				}

				$values[ $property['fieldName'] ] = $value;
			}

			if ( array_key_exists( 'belongsTo', $property ) ) {
				
				$reflectionProperty = $this->reflectionClass->getProperty( $property['property'] );
				$reflectionProperty->setAccessible(true);

				$object = $reflectionProperty->getValue( $this->modelClass );

				$objectEngine = new Engine( $object );

				$ppk = $objectEngine->getMetaObject()['propertyPK'];

				$refClass = $objectEngine->getReflectionClass();

				$reflectionProperty2 = $refClass->getProperty( $ppk );
				$reflectionProperty2->setAccessible(true);

				$values[ $property['fieldName'] ] = $reflectionProperty2->getValue( $object ); 
			}
		}

		$ppk = $this->metaObject['propertyPK'];

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

		$ppk = $this->metaObject['propertyPK'];

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

	public function getModel() {

		return $this->modelClass;
	}

	public function getFetchedObjets() {

		return $this->fetchedObjects;
	}

	public function toJson ( $convert = true ) {


	}

	public function fetch( $property ) {

		if (array_key_exists($property, $this->metaObject['properties']) and 
			array_key_exists('belongsto',$this->metaObject['properties'][$property])) {

			foreach ($this->fetchedObjects as $key => $object) {
				
				$reflectionProperty = $this->reflectionClass->getProperty( $this->metaObject['propertyPK'] );
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
				"La propiedad '$property' no representa a un obejto o coleccion de objetos");
		}
	}

	public function addParentEngine ( $className , $engine ) {

		$this->parentEngines[$className] = $engine;
	}
}

