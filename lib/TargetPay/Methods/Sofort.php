<?php
namespace TargetPay;

use TargetPay\Transaction as Transaction;

/**
 *  TargetPay transaction SDK - iDEAL
 * 
 *  @author     Yellow Melon B.V.
 *  @release    26-10-2014
 *  @ver        1.0
 */

class Methods_Sofort extends Transaction
{
    /**
     *  @inheritdoc
     */

    protected $name                 = "Sofort Banking";
    protected $method               = "DEB";        
    protected $checkApi             = "https://www.targetpay.com/directebanking/check";
    protected $minimumAmount        = 10;       
    protected $maximumAmount        = 500000;   
    protected $languages            = array('de', 'en', 'nl');

    /**
     * Country ID
     */

    protected $country = null;

    /**
     *  Set country ID: 49=Germany 43=Austria 41=Switzerland
     *  @param int $country 
     */

    public function country ($country) 
    {
        $this->country = (int) $country;
        return $this;
    }

    /**
     *  Add country ID to start request
     *  @param Request $request 
     */

    public function beforeStart($request)
    {
        if (!$this->country) throw new Exception ("No country selected for Sofort Banking");
        $request->bind ("country", $this->country);
        $request->bind ("type", 1);
    }

    /**
     *  Provide static instance of this model
     */

    public static function model() 
    {
        return new self;
    }   
}

