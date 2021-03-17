<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
	const MESSAGES = [
		1500 => 'There\'s something wrong. Please try again later.',
		1501 => 'You have no such product in your cart.',
		1502 => 'You have no unpaid order.',
		1503 => 'You have no item in your cart.',
		1504 => 'Insufficient stock.',
	];

    public function __construct($err_code, $http_status) {
    	parent::__construct(isset(self::MESSAGES[$err_code]) ? self::MESSAGES[$err_code] : self::MESSAGES[1500], $http_status);
    }
}
