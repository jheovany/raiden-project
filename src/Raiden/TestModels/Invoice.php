<?php

namespace Raiden\TestModels;


/**
 * @table Invoice
 */
class Invoice {

	/**
	 * @field id 
	 * @constraint ["primary key", "not null"]
	 */
	private $id;

	/**
	 * @field idClient 
	 * @constraint [{"has one": "Client"}, "not null"]
	 */
	private $client;

	/**
	 * @field accountNumber 
	 * @constraint ["not null"]
	 */
	private $accountNumber;

	/**
	 * @field invoicedFrom 
	 * @constraint ["not null"]
	 */
	private $invoicedFrom;

	/**
	 * @field invoicedTo 
	 * @constraint ["not null"]
	 */
	private $invoicedTo;

	/**
	 * @field id
	 * @type double 
	 * @constraint ["primary key", "not null"]
	 */
	private $total;

	/**
	 * @field invoiceDetails 
	 * @constraint {"has many": "InvoiceDetails"}
	 */
	private $invoiceDetails;

	public function getId()	{

		return $this->id;
	}
}