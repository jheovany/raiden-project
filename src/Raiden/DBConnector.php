<?php

namespace Raiden;

class DBConnector {

	private $PDOConnector;

	private $config;

	public function __construct ( ) {

		$this->initialize();
	}

	public function initialize() {

		$this->config = include 'raiden.config.php';

		$connector = $this->config['default-connector'];
		$host = $this->config['connectors'][$connector]['host'];
		$port = $this->config['connectors'][$connector]['port'];
		$database = $this->config['connectors'][$connector]['database'];
		$user = $this->config['connectors'][$connector]['username'];
		$pass = $this->config['connectors'][$connector]['password'];

		if ($connector == "oci") {

			$dsn = "$connector:dbname=$database";	

		} else {

			$dsn = "$connector:host=$host;port=$port;dbname=$database";
		}//var_dump($dsn);

		
		try {
			$this->PDOConnector = new \PDO($dsn, $user, $pass);
		} catch (PDOException $e) { 
			var_dump($e->getMessage());
			die();	
		}

	}

	public function connector ( ) {

		return $this->PDOConnector;
	}
}