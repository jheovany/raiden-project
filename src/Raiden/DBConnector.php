<?php

namespace Raiden;

class DBConnector {

	private $PDOConnector;

	private $config;

	public function initialize() {

		$this->Config();

		$connector = $this->config['default-connector'];
		$host = $this->config['connectors'][$connector]['host'];
		$database = $this->config['connectors'][$connector]['database'];
		$user = $this->config['connectors'][$connector]['username'];
		$pass = $this->config['connectors'][$connector]['password'];

		$dsn = "$connector:host=$host;dbname=$database";
		var_dump($dsn);

		
		try {
			$this->PDOConnector = new \PDO($dsn, $user, $pass);
		} catch (PDOException $e) { 
			var_dump($e->getMessage());
			die();	
		}
	}

	public function Config() {

		$this->config = include 'raiden.config.php';

		var_dump($this->config);
	}
}