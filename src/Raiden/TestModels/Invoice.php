<?php

namespace Raiden\TestModels;


/**
 * @table invoice
 */
class Invoice {

	/**
	 * @field id
	 * @PK
	 */
	private $id;

	/**
	 * @field id_client
	 * @constraint ["not null"]
	 * @hasone Raiden\TestModels\Client
	 */
	private $client;

	/**
	 * @field total
	 * @type double 
	 * @constraint ["primary key", "not null"]
	 */
	private $total;

	/**
	 * @hasmany Raiden\TestModels\InvoiceDetails
	 */
	private $invoiceDetails;

	public function getClient()	{

		return $this->client;
	}
}