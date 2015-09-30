<?php

namespace Raiden\SQLBuilder;

/**
* 
*/
class Statements  {
	
	private $table;	

	private $columns = [];

	private $sqlStatements = [];

	function __construct() {
		$this->init();
	}

	private function init() {
		$this->sqlStatements['insert'] = 
		['INSERT INTO','table'=>null,'(','columns'=>null,')','VALUES','(','values'=>null,')'];
	}

	public function setTable ( $table ) {

		$this->table = $table;
		$this->sqlStatements['insert']['table'] = $this->table;
	}

	public function addColumn ( $column ) {

		$this->columns[] = $column;
	}

	public function getInsert($data) {
		
		$columns = null;
		$values = null;

		foreach ($data as $k => $d) {
			
			$columns[] = $k; 
			$values[] = $d;
		}

		echo 'keys:';
		var_dump($columns);
		echo 'values:';
		var_dump($values);


		//$this->sqlStatements['insert']['columns'] = implode( ', ' , $this->columns );
		//$this->sqlStatements['insert']['values'] = implode( ', ' , $values );
		
		//$insert = implode( ' ', $this->statement );
		$this->sqlStatements['insert']['columns'] = implode(', ',$columns);
		$this->sqlStatements['insert']['values'] = implode(', ',$values);
		
		$insert = implode(' ',$this->sqlStatements['insert']); 

		echo 'insert:';
		var_dump($insert);

		return $insert;
	}
}