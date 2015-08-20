<?php

namespace Raiden\SQLBuilder;

class SelectStatement {

	private $table;	

	private $columns = [];

	private $statement;

	public function setTable ( $table ) {

		$this->table = $table;
	}

	public function addColumn ( $column ) {

		$this->columns[] = $column; 
	}
}