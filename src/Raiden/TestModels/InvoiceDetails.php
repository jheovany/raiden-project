<?php

namespace Raiden\TestModels;

/**
 * @table invoice_details
 */
class InvoiceDetails {

	/**
	 * @field id
	 * @PK
	 */
	private $id;

	/**
	 * @field total
	 * @type double
	 */
	private $total;
}