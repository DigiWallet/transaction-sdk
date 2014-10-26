<?php
namespace TargetPay;

/**
 *  TargetPay transaction SDK - Response of a start payment
 * 
 *  @author     Yellow Melon B.V.
 *  @release    26-10-2014
 *  @ver        1.0
 */

class StartResponse
{
    public $status;         // true/false, succesful or not 
    public $url;            // URL for redirect if successful AND applicable => either URL or payinfo are filled
    public $payinfo;        // Payment info to display => either URL or payinfo are filled
    public $error;          // Error message, if unsuccesful

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

