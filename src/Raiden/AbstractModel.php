<?php

namespace Raiden;

/**
* @author  Jheovany Menjivar
* @abstract
* @version 0.1
*/
abstract class AbstractModel {

	const CREATED = 'CREATED';

	const SELECTED = 'SELECTED';
	
	private $_MODEL_STATUS_ = self::CREATED;

	/**
	 * @var Array
	 */
	private $_CURRENT_DATA_ = [];
}