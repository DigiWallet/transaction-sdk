<?php
namespace TargetPay;

use TargetPay\Transaction as Transaction;

/**
 *	TargetPay transaction SDK - Mister Cash
 * 
 * 	@author	 	Yellow Melon B.V.
 * 	@release	26-10-2014
 * 	@ver		1.0
 */

class Methods_MisterCash extends Transaction
{
	/**
	 *	@inheritdoc
	 */

	protected $name 				= "Bancontact/Mister Cash";
    protected $method				= "MRC";		
	protected $checkApi             = "https://www.targetpay.com/mrcash/check";
	protected $minimumAmount        = 49;		
	protected $maximumAmount        = 500000;	
    protected $currencies 			= array('EUR'); 
    protected $languages 			= array('nl', 'fr', 'en');

	/**
	 *	Provide static instance of this model
	 */

	public static function model() 
	{
		return new self;
	}	
}

