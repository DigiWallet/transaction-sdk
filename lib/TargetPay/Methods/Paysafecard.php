<?php
namespace TargetPay;

use TargetPay\Transaction as Transaction;

/**
 *	TargetPay transaction SDK - Paysafecard
 * 
 * 	@author	 	Yellow Melon B.V.
 * 	@release	26-10-2014
 * 	@ver		1.0
 */

class Methods_Paysafecard extends Transaction
{
	/**
	 *	@inheritdoc
	 */

	protected $name 				= "Paysafecard";
    protected $method				= "WAL";		
	protected $checkApi             = "https://www.targetpay.com/paysafecard/check";
	protected $minimumAmount        = 10;		
	protected $maximumAmount        = 15000;	
    protected $currencies 			= array('EUR'); 
    protected $languages 			= array('nl');

	/**
	 *	Provide static instance of this model
	 */

	public static function model() 
	{
		return new self;
	}	
}

