<?php
namespace TargetPay;

use TargetPay\Request;
use TargetPay\Exception;
use TargetPay\StartResponse;
use TargetPay\CheckResponse;

/**
 *	TargetPay transaction SDK - Abstract base class
 * 
 * 	@author	 	Yellow Melon B.V.
 * 	@release	26-10-2014
 * 	@ver		1.0
 */

abstract class Transaction 
	{

	/**
	 * Salt to use
	 */

	const SALT = '932kvm8937*#&1nj_aa9873j0a0987'; 

	/**
	 * Official name
	 */

	protected $name = null;					

	/**
	 * Payment method ID
	 */

	protected $method = null;				

	/**
	 * Check API URL
	 */

	protected $checkApi = null;				

	/**
	 * Official name
	 */

	protected $minimumAmount = 84; 	

	/**
	 * Minimum amount
	 */

	protected $maximumAmount = 1000000;		

	/**
	 *	Currencies available, first is default
	 */

	protected $currencies = array('EUR');		

	/**
	 *	Languages available, first is default
	 */

    protected $languages = array('nl');		

	/**
	 *	TargetPay layoutcode
	 */

	protected $rtlo = null;

	/**
	 *	Test mode on/off
	 */

	protected $test = false;

	/**
	 *	Language, will be set to the default for the payment method if not explicitly defined
	 */

    protected $language	= null;	

	/**
	 *	Default currency, will be set to the default for the payment method if not explicitly defined
	 */

    protected $currency	= null;	

	/**
	 *	Application ID
	 */

	protected $appId = '3087f78657faca499cf526ae90340948'; 

	/**
	 *	Amount in Eurocents 
	 */

	protected $amount = 0;		

	/**
	 *	Description for bank statement
	 */

	protected $description = null;	

	/**
	 *	Return URL
	 */

	protected $returnUrl = null;		

	/**
	 *	Cancel URL
	 */

	protected $cancelUrl = null;		

	/**
	 *	Report URL
	 */

	protected $reportUrl = null;		

	/**
	 *	Transaction ID
	 */

	protected $txid = null;		

	/**
	 *	Called before start call so additional parameters can be added to the request
	 *  May be implemented by specfic payment method where needed
	 */

	public function beforeStart($request) {}

	/**
	 *	Called after start to process http request to a response
	 */

	public function parseStartResponse($httpResponse) 
	{
        if (substr($httpResponse, 0, 6)=="000000") {
            $httpResponse = explode("|", substr($httpResponse, 7));
            $this->txid = $httpResponse[0]; // For immediate reuse of the object
            return new StartResponse(array("status" => true, "txid" => $httpResponse[0], "url" => $httpResponse[1]));
 		} else {
 			return new StartResponse(array("status" => false, "error" => $httpResponse));
		}
	}

    /**
	 *  Start transaction at TargetPay
	 */

	public function start () 
	{
		if (!$this->amount) throw new Exception ("No amount given");
        if ($this->amount < $this->minimumAmount) throw new Exception ("Amount is too low: minimum=" . $this->minimumAmount);
        if ($this->amount > $this->maximumAmount) throw new Exception ("Amount is too high: maximum=" . $this->maximumAmount);
      
      	// Create request object
      	$request = new Request("https://www.targetpay.com/api/idealplugins");

      	// Fill it up
        $request->bind (
        	array(
	        	"paymethod" => $this->method,
				"rtlo" => $this->rtlo,
		        "amount" => $this->amount,
	        	"description" => $this->description, 
	        	"reporturl" => $this->reportUrl,
	        	"returnurl" => $this->returnUrl,
		        "cancelurl" => $this->cancelUrl,
			    "app_id" => $this->appId,
			    "language" => ($this->language) ? $this->language : $this->languages[0],
			    "lang" => ($this->language) ? $this->language : $this->languages[0],
			    "currency" => ($this->currency) ? $this->currency : $this->currencies[0],
	        	"userip" => (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "cli"),
	        	"domain" => (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["HTTP_HOST"] : "cli"),
	        	"salt" => self::SALT
	        )
		);

        // Invoke on before start event
        $this->beforeStart($request);

        // Do http call
        $httpResponse = $request->execute ();

        // Make start response object and return it
        return $this->parseStartResponse ($httpResponse);
	}

	/**
	 *	Called after check to process http request to a response
	 */

	public function parseCheckResponse($httpResponse) 
	{
		if (substr($httpResponse,0,6) == "000000") {
			$info = explode("|", $httpResponse);
			return new CheckResponse(
				array(
					"status" => true,
					"name" => (isset($info[1]) ? $info[1] : null),
					"iban" => (isset($info[2]) ? $info[2] : null)
				)
			);
		} else {
			return new CheckResponse(array("status" => false, "error" => $httpResponse));
		}
	}	

    /**
	 *	Check transaction with TargetPay
     */

    public function check () 
    {
      	// Create request object
      	$request = new Request($this->checkApi);
      	
      	// Fill it up
		$request->bind(
			array(
				"rtlo" => $this->rtlo,
				"trxid" => $this->txid,
				"test" => $this->test,
				"checksum" => md5($this->txid . $this->rtlo . self::SALT)
			)
		);

		// Run check
		$httpResponse = $request->execute();

        // Make check response object and return it
        return $this->parseCheckResponse ($httpResponse);		
    }

	/**
	 * 	Get method code
	 */

	public function getMethod()
	{
		return $this->method;
	}

	/**
	 *	Set the amount, as of now it is always in cents [start]
	 *  @param int $amount
	 */

	public function amount ($amount)
	{
    	$this->amount = round($amount);
		return $this;
	}

	/**
	 *	Set the app ID [start, check]
	 *  @param string $appId
	 */

	public function appId ($appId)
	{
		$this->appId = strtolower(preg_replace("/[^a-z\d_]/i", "", $appId));
		return $this;
	}

	/**
	 *	Set the currency. See documentation for available currencies [start]
	 *  @param string $currency
	 */

	public function currency ($currency) 
	{
		if (in_array($currency, $this->currencies)) {
			$this->currency = $currency;
		}
		return $this;
	}

	/**
	 *	Set description for on the banking statement [start]
	 *  @param string $description
	 */

	public function description ($description) 
	{
    	$this->description = substr($description, 0, 32);
		return $this;
	}

	/**
	 *	Set the language [start]
	 *  @param string $language
	 */

	public function language ($language)
	{
		if (in_array($language, $this->languages)) {
			$this->language = $language;
		}
		return $this;
	}

	/**
	 *	Set the report URL [start]
	 *  @param string $reportUrl
	 */

	public function reportUrl ($reportUrl) 
	{
		if (preg_match('|(\w+)://([^/:]+)(:\d+)?(.*)|', $reportUrl)) $this->reportUrl = $reportUrl;
		return $this;
	}

	/**
	 *	Set the return URL [start]
	 *  @param string $reportUrl
	 */

	public function returnUrl ($returnUrl) 
	{
		if (preg_match('|(\w+)://([^/:]+)(:\d+)?(.*)|', $returnUrl)) $this->returnUrl = $returnUrl;
		return $this;
	}

	/**
	 *	Set the cancel URL [start]
	 *  @param string $cancelUrl
	 */

	public function cancelUrl ($cancelUrl) 
	{
		if (preg_match('|(\w+)://([^/:]+)(:\d+)?(.*)|', $cancelUrl)) $this->cancelUrl = $cancelUrl;
		return $this;
	}

	/**
	 *	Set the layoutcode [start, check]
	 *  @param int $rtlo
	 */

	public function rtlo ($rtlo)
	{
		$this->rtlo = $rtlo;
		return $this;
	}

	/**
	 *	Enable/disable testmode [check]
	 *  @param bool $test
	 */

	public function test ($test)
	{
		$this->test = (bool) $test;
		return $this;
	}

	/**
	 *	Set transaction ID [check]
	 *  @param string $txid
	 */

	public function txid ($txid) 
	{
        $this->txid = substr($txid, 0, 32);
		return $this;
	}

	/**
	 *	Provide static instance of a payment model based on its class name
	 *	@param string $method Class name of the method, e.g. Ideal
	 */

	public static function model($method)
	{
		$class = '\\TargetPay\\Methods_'.$method;
		return new $class;
	}

}