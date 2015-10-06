<?php

return [

	/**
	 * Base de datos por defecto, cambiar por la base de datos que
	 * se requiera usar ej: mysql, postgres, oracle.
	 */
	'default-connector' => 'mysql',
	
	'connectors' => [

		'mysql' => [
			'host'     => 'localhost',
			'database' => 'test',
			'username' => 'root',
			'password' => '',
		],

		'postgres' => [
			'host'     => 'localhost',
			'database' => '',
			'username' => '',
			'password' => '',
		],

		'oracle' => [
			'host'     => 'localhost',
			'database' => '',
			'username' => '',
			'password' => '',
		]
	]	
];


