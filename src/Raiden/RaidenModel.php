<?php

namespace Raiden;

/**
* 
*/
abstract class RaidenModel {

	const CREATED = 'CREATED';

	const SELECTED = 'SELECTED';
	
	private $_MODEL_STATUS_ = self::CREATED;

	private $_CURRENT_DATA_;
}