<?php

namespace Raiden\TestModels\InvoicingModels;

/**
 * @table invoice
 */
class Invoice {

	/** 
	 * @field id_invoice @PK @auto
	 * 
	 */
	private $id;

	/**
	 * @field current_reading
	 * @type int
	 * 
	 */
	private $current_reading;

	/**
	 * @field month_consumed
	 * @type int
	 * 
	 */
	private $month_consumed;

	/**
	 * @field invoiced_from
	 * @type date 
	 * 
	 */
	private $invoiced_from;

	/**
	 * @field invoiced_until
	 * @type date
	 * 
	 */
	private $invoiced_until;

	/**
	 * @field invoiced_days
	 * @type tinyint
	 * 
	 */
	private $invoiced_days;

	/**
	 * @field invoiced_month
	 * @type varchar
	 * 
	 */
	private $invoiced_month;

	/**
	 * @field charge_month
	 * @type float
	 * 
	 */
	private $charge_month;

	/**
	 * @field debt
	 * @type float
	 */
	private $debt;

	/**
	 * @field client_id_client
	 * @belongsTo Raiden\TestModels\InvoicingModels\Client
	 */
	private $client;

	/**
	 * @field provider_id_provider
	 * @belongsTo Raiden\TestModels\InvoicingModels\Provider
	 */
	private $provider;

	/**
	 * @hasMany Raiden\TestModels\InvoicingModels\InvoiceDetails
	 * @FK invoice_id
	 */
	private $details;

	public function setValues ( $client, $details ) {

		$this->client = $client;
		$this->details = $details;
	}

	public function getId(){
		return $this->id;
	}

	public function getCurrentReading(){
		return $this->current_reading;
	}

	public function setCurrentReading($current_reading){
		$this->current_reading = $current_reading;
	}

	public function getMonthConsumed(){
		return $this->month_consumed;
	}

	public function setMonthConsumed($month_consumed){
		$this->month_consumed = $month_consumed;
	}

	public function getInvoicedFrom(){
		return $this->invoiced_from;
	}

	public function setInvoicedFrom($invoiced_from){
		$this->invoiced_from = $invoiced_from;
	}

	public function getInvoicedUntil(){
		return $this->invoiced_until;
	}

	public function setInvoicedUntil($invoiced_until){
		$this->invoiced_until = $invoiced_until;
	}

	public function getInvoicedDays(){
		return $this->invoiced_days;
	}

	public function setInvoicedDays($invoiced_days){
		$this->invoiced_days = $invoiced_days;
	}

	public function getInvoicedMonth(){
		return $this->invoiced_month;
	}

	public function setInvoicedMonth($invoiced_month){
		$this->invoiced_month = $invoiced_month;
	}

	public function getChargeMonth(){
		return $this->charge_month;
	}

	public function setChargeMonth($charge_month){
		$this->charge_month = $charge_month;
	}

	public function getDebt(){
		return $this->debt;
	}

	public function setDebt($debt){
		$this->debt = $debt;
	}	

	public function getClient()	{
		return $this->client;
	}

	public function setClient( $client ) {
		$this->client = $client;
	}

	public function getProvider(){
		return $this->provider;
	}

	public function setProvider($provider){
		$this->provider = $provider;
	}
	
	public function  getDetails() {
		return $this->details;
	}

	public function addDetail($det) {
		$this->details[] = $det;
	}
}