<?php

return [

	/**
	 * Base de datos por defecto, cambiar por la base de datos que
	 * se requiera usar ej: mysql, pgsql, oracle.
	 */
	'default-connector' => 'mysql',
	//'default-connector' => 'pgsql',
	//'default-connector' => 'oci',
	
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

		'oci' => [
			//'host'     => 'localhost',
			//'port'     => '1521',
			'database' => 'localhost/xe:1521',
			'username' => 'test',
			'password' => 'admin',
		]
	]	
];


