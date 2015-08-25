<?php

namespace Raiden;

class Connect {

	private $connection;

	public function getConnection() {

		if ( !$this->connection ) {

			$this->connection = new \PDO('mysql:host=localhost;dbname=test', 'root');			
		} 

		return $this->connection;
	}
}