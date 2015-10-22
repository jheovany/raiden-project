<?php

return [

	/**
	 * Base de datos por defecto, cambiar por la base de datos que
	 * se requiera usar ej: mysql, pgsql, oracle.
	 */
	//'default-connector' => 'mysql',
	'default-connector' => 'pgsql',
	//'default-connector' => 'oracle',
	
	'connectors' => [

		'mysql' => [
			'host'     => 'localhost',
			'port'     => '3306',
			'database' => 'test',
			'username' => 'root',
			'password' => '',
		],

		'pgsql' => [
			'host'     => 'localhost',
			'port'     => '5432',
			'database' => 'test',
			'username' => 'postgres',
			'password' => 'admin',
		],

		'oracle' => [
			'host'     => 'localhost',
			'database' => '',
			'username' => '',
			'password' => '',
		]
	]	
];


