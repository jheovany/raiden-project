<?php

namespace Raiden\TestModels;


/**
 *
 * @table Invoice
 * 
 */
class Invoice {

	/**
	 *
	 * @field id 
	 * @constraint ["primary key", "not null"]
	 * 
	 */
	private $id;

	private $idClient;

	private $accountNumber;

	private $invoicedFrom;

	private $invoicedTo;

	private $total;

	/**
	 *
	 * @field invoiceDetails 
	 * @constraint {"references": "InvoiceDetails"}
	 * 
	 */
	private $invoiceDetails;

	public function getId()	{

		return $this->id;
	}
}