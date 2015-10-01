<?php

namespace Raiden\SQLBuilder;

/**
* 
*/
class UpdateStatement {
	
	private $table;	

	private $sqlStatements = [];

	function __construct() {
		$this->init();
	}

	private function init() {
		$this->sqlStatements['update'] = 
		['UPDATE','table'=>null,'SET','values'=>null,'WHERE','cond'=>null];
	}

	public function setTable ( $table ) {

		$this->table = $table;
		$this->sqlStatements['update']['table'] = $this->table;
	}

	public function getUpdate($data, $cond) {

		$values = null;

		foreach ($data as $k => $d) {
			
			$values[$k] = $k.' = '.$d;
		}

		echo 'values';
		var_dump($values);

		$this->sqlStatements['update']['values'] = implode(', ',$values);
		
		$update = implode(' ',$this->sqlStatements['update']) . $cond;

		echo 'update:';
		var_dump($update);

		return $update;
	}
}