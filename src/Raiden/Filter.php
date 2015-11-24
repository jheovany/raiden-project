<?php

namespace Raiden;

/**
* 
*/
class Filter {
	
	private $engine;

	private $metaObject;

	private $sqlFilter;

	private $isIn;

	function __construct ( ) {
		

	}

	public function initialize ( $engine ) {

		$this->engine = $engine;
		$this->metaObject = $this->engine->getMetaObject();
	}

	public function where ( $propery ) {

		$props = explode(".", $propery);
		$prop = (string) array_shift($props);

		if ( array_key_exists ( $prop,  $this->metaObject['properties'] ) ) {

			
			if ( array_key_exists ( 'belongsTo',  $this->metaObject['properties'][$prop] ) ) {
				// Subtabla
			
				$fk = $this->metaObject['properties'][$prop]['fieldName'];

				$engine = $this->metaObject['properties'][$prop]['engine'];

				$pk = $engine->getMetaObject()['PK'];

				$table = $engine->getMetaObject()['tableName'];	

				$subp = implode(".",$props);

				if ($subp == '') throw new \Exception("Al parecer la propiedad '$prop' es un objeto y no una constante.");

				$cond = $engine->getFilter()->where( $subp )->getSqlFilter();

				$sql = "$fk IN ( SELECT $pk FROM $table WHERE $cond";

				$this->sqlFilter = $sql;

				//var_dump($sql);

				$this->isIn = true;

			}

			else if ( array_key_exists ( 'hasMany',  $this->metaObject['properties'][$prop] ) ) {
				// Subtabla

				$pk = $this->metaObject['PK'];
				
				$engine = $this->metaObject['properties'][$prop]['engine'];

				$fk = $this->metaObject['properties'][$prop]['FK'];

				$table = $engine->getMetaObject()['tableName'];

				$subp = implode(".",$props);				

				if ($subp == '') 
					throw new \Exception(
						"Al parecer la propiedad '$prop' 
						representa a un objeto y no a una constante.");

				$cond = $engine->getFilter()->where( $subp )->getSqlFilter();

				$sql = "$pk IN ( SELECT $fk FROM $table WHERE $cond";

				$this->sqlFilter = $sql;

				//var_dump($sql);

				$this->isIn = true;
			}

			else {

				$this->sqlFilter = $this->metaObject['properties'][$prop]['fieldName'];

				$this->isIn = false;
			}
		} 

		else {

			throw new \Exception( "La propiedad $prop no existe" );
		}

		return $this;
	} 

	public function equal ( $value ) {

		if (is_string($value)){
			$value = "'$value'";
		}

		$this->sqlFilter = $this->sqlFilter . " = " . $value;

		return $this;
	}

	public function notEqual ( $value ) {

		if (is_string($value)){
			$value = "'$value'";
		}

		$this->sqlFilter = $this->sqlFilter . " <> " . $value;

		return $this;
	}

	public function greaterThan ( $value ) {

		if (is_string($value)){
			$value = "'$value'";
		}

		$this->sqlFilter = $this->sqlFilter . " > " . $value;

		return $this;	
	}
	public function lessThan ( $value ) {

		if (is_string($value)){
			$value = "'$value'";
		}

		$this->sqlFilter = $this->sqlFilter . " < " . $value;

		return $this;
	}

	public function greaterThanOrEqual ( $value ) {

		if (is_string($value)){
			$value = "'$value'";
		}

		$this->sqlFilter = $this->sqlFilter . " >= " . $value;

		return $this;
	}

	public function lessThanOrEqual ( $value ) {

		if (is_string($value)){
			$value = "'$value'";
		}

		$this->sqlFilter = $this->sqlFilter . " <= " . $value;

		return $this;
	}
	
	public function between ( $value1, $value2 ) { 

		if (is_string($value1)){
			$value1 = "'$value1'";
			$value2 = "'$value2'";
		}

		$this->sqlFilter = $this->sqlFilter . " BETWEEN " . $value1 . " AND " . $value2;

		return $this;
	}
	
	public function like ( $value ) {

		
		if (is_string($value)){
			$value = "'%$value%'";
		}

		$this->sqlFilter = $this->sqlFilter . " LIKE $value";

		return $this;
	}
	
	public function in ( $values ) {

		$this->sqlFilter = $this->sqlFilter . " IN ( " . implode(", ", $values) . " )";

		return $this;
	}
	
	//public function or ( $propery ) { }
	//public function and ( $propery ) { }

	public function getSqlFilter() {

		if ($this->isIn) {
			
			$this->sqlFilter = $this->sqlFilter . " )";

		}

		//var_dump($this->sqlFilter);

		return $this->sqlFilter;
	}
}