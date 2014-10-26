<?php
namespace TargetPay;

use TargetPay\Transaction as Transaction;
use TargetPay\Request as Request;

/**
 *  TargetPay transaction SDK - iDEAL
 * 
 *  @author     Yellow Melon B.V.
 *  @release    26-10-2014
 *  @ver        1.0
 */

class Methods_Ideal extends Transaction
{
    /**
     *  @inheritdoc
     */

    protected $name                 = "iDEAL";
    protected $method               = "IDE";        
    protected $checkApi             = "https://www.targetpay.com/ideal/check";
    protected $minimumAmount        = 84;           
    protected $maximumAmount        = 1000000;      
    protected $currencies           = array('EUR'); 
    protected $languages            = array('nl'); 

    /**
     *  Bank ID
     */

    protected $bank                 = null; // Bank ID

    /**
     *  Set bank ID
     *  @param string $bank 4-digit bank code, obtained by Ideal::bankList()
     */

    public function bank ($bank) 
    {
        $this->bank = substr($bank, 0, 4);
        return $this;
    }

    /**
     *  Get list with bank codes
     *  @param string $bank 4-digit bank code, obtained by Ideal::bankList()
     *  @return array List with banks
     */

    public function bankList() 
    {
        $request = new Request("https://www.targetpay.com/api/idealplugins?banklist=IDE");

        $xml = $request->execute();
        if (!$xml) {
            $banks_array["IDE0001"] = "Bankenlijst kon niet opgehaald worden bij TargetPay, controleer of curl werkt!";
            $banks_array["IDE0002"] = "  ";
        } else {
            $banks_object = new SimpleXMLElement($xml);
            foreach ($banks_object->bank as $bank) 
                $banks_array["{$bank->bank_id}"] = "{$bank->bank_name}";
        }
        return $banks_array;
    }

    /**
     *  Add bank ID to start request
     *  @param $request Request
     */

    public function beforeStart($request)
    {
        if (!$this->bank) throw new Exception ("No bank selected for iDEAL");
        $request->bind ("bank", $this->bank);
        $request->bind ("ver", 2);
        $request->bind ("language", "nl");
    }

    /**
     *  Provide static instance of this model
     */

    public static function model() 
    {
        return new self;
    }   
}

