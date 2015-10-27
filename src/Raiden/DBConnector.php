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
		$database = $this->config['connectors'][$connector]['database'];
		$user = $this->config['connectors'][$connector]['username'];
		$pass = $this->config['connectors'][$connector]['password'];

		if ($connector == "oci") {

			$database = $this->config['connectors'][$connector]['database'];
			$dsn = "$connector:dbname=$database";
				
		} else {

			$host = $this->config['connectors'][$connector]['host'];
			$port = $this->config['connectors'][$connector]['port'];

			$dsn = "$connector:host=$host;port=$port;dbname=$database";
		}

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