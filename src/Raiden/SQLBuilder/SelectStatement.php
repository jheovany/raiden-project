<?php

namespace Raiden\SQLBuilder;

class SelectStatement /*extends SQLStatement*/ {

	private $table;	

	private $columns = [];

	private $statement = ['SELECT', 'c'=>null, 'FROM', 't'=>null,  'w'=>null];

	private $condition;

	public function setTable ( $table ) {

		$this->table = $table;
		$this->statement['t'] = $this->table;
	}

	public function addColumn ( $column ) {

		$this->columns[] = $column;
	}

	public function getSelect() {
		
		$this->statement['c'] = implode( ', ' , $this->columns );
		$select = implode( ' ', $this->statement );

		return $select;
	}

	public function setCondition ( $condition ) {

		$this->condition = $condition;
		$this->statement['w'] = $condition;
	}
}