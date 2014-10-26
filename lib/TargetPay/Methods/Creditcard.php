<?php
namespace TargetPay;

use TargetPay\Transaction as Transaction;

/**
 *  TargetPay transaction SDK - Visa/Mastercard
 * 
 *  @author     Yellow Melon B.V.
 *  @release    26-10-2014
 *  @ver        1.0
 */

class Methods_Creditcard extends Transaction
{
    /**
     *  @inheritdoc
     */

    protected $name                 = "Visa/Mastercard";
    protected $method               = "CC";     
    protected $checkApi             = "https://www.targetpay.com/creditcard_atos/check";
    protected $minimumAmount        = 100;      
    protected $maximumAmount        = 1000000;  
    protected $currencies           = array('EUR'); 
    protected $languages            = array('nl');

    /**
     *  Provide static instance of this model
     */

    public static function model() 
    {
        return new self;
    }   
}

