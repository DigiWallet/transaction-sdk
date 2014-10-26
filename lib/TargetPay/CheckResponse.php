<?php
namespace TargetPay;

/**
 *  TargetPay transaction SDK - Response of a payment check
 * 
 *  @author     Yellow Melon B.V.
 *  @release    26-10-2014
 *  @ver        1.0
 */

class CheckResponse
{
    public $status;         // true/false, succesful or not 
    public $error;          // Error message, if unsuccesful

    public $iban;           // IBAN of the customer, when implemented by the payment method
    public $name;           // Name of the customer, when implemented by the payment method

    /**
     *  Constructor, fill object based on array
     */

    public function __construct ($values = null)
    {
        if (isset($values) && is_array($values)) {
            foreach ($values as $property =>$value) {
                $this->$property = $value;
            }
        }
    }
}

