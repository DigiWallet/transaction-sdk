<?php
namespace TargetPay;

use Response;

/**
 *	TargetPay transaction SDK - HTTP request class
 * 
 * 	@author	 	Yellow Melon B.V.
 * 	@release	26-10-2014
 * 	@ver		1.0
 */

class Request
{
	/**
	 *  Debug mode, enable to see exact requests
	 */

	const DEBUG = false;

	/**
	 *	URL to call
	 */

	protected $url = null;	

	/**
	 *	Method to call the URL with
	 */

	protected $method = "GET";

	/**
	 *	Parameters 
	 */

	protected $parameters = array();	

	/**
	 *  Constructor, set URL and method
	 *	@param string $url URL to call
	 *	@param string $method Method to use, GET or POST
	 */

	public function __construct ($url, $method = "GET")
	{
		$this->url = $url;
		$this->method = $method;
	}

	/**
	 *	Bind parameter or a array of parameters
	 *  
	 *  @param mixed $fields Fieldname or array
	 *  @param mixed $value It's new value (may be omitted in use of array)
	 */

	public function bind ($fields, $value = null) 
	{
		if (is_array($fields)) {
			foreach ($fields as $field => $value) {
				$this->parameters[$field] = $value;
			}
		} else {
			$this->parameters[$fields] = $value;
		}
		return $this;
	}

	/**
	 *	Prepare query string
	 *	@param string $url URL to append to
	 *	@return URL with parameters attached
	 */

	private function addQueryString ($url)
	{
		if (!$this->parameters) return $url;
    	
    	$queryString = "";
    	foreach ($this->parameters as $key => $value) 
    		$queryString .= "&" . $key . "=" . urlencode($value);

    	if (self::DEBUG) {
    		var_dump ($this->parameters);
    	}

    	return $url . "?" . substr($queryString, 1);
	}

	/**
	 *	Call the URL
	 *	@return Raw HTTP response
	 */

    public function execute () 
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->addQueryString ($this->url));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($this->method=="POST") {
        	curl_setopt($ch, CURLOPT_POST, 1);
        }

        $httpResult = curl_exec($ch);
		curl_close($ch);

        return $httpResult;
	}

}

