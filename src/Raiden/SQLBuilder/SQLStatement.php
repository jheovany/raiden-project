<?php

/**
* 
*/
class SQLStatement {
	
	private $table;

	private $sqlStatements = [];

	function __construct( $table ) {

		$this->initialize( $table ); 
	}

	public function initialize($table) {

		$this->table = $table;

		$this->sqlStatements['insert'] = 
		['INSERT INTO','table'=>null,'(','columns'=>null,')','VALUES','(','values'=>null,')'];

		$this->sqlStatements['select'] =
		['SELECT', 'columns'=>null, 'FROM', 'table'=>null,'WHERE', 'cond'=>null];

		$this->sqlStatements['update'] = 
		['UPDATE','table'=>null,'SET','values'=>null,'WHERE','cond'=>null];

		$this->sqlStatements['delete'] = 
		"DELETE FROM $table WHERE $pk = $pkVal";
	}
}
