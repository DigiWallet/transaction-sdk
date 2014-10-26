#!/usr/bin/env php
<?php
require_once __DIR__.'/../lib/TargetPay/autoload.php';

use \TargetPay as TargetPay;

/**
 *	Function to test TargetPay calls for the various payment methods
 *	Test parameters
 *	- rtlo 					121455 (change this!)
 *	- description   		Test payment
 *  - bank for iDEAL		0021 (Rabobank)
 *  - country for Sofort   	49 (Germany)
 *  - ...
 *
 *  After payment start, the payment is checked
 */

function test ($method) 
{
	echo "------------------------------------------------------------------------------\n";
	echo "$method : ";

	// Start payment

	$startPayment = TargetPay\Transaction::model($method)
	 	->rtlo(121455)
	 	->amount(1000)
	 	->description('Test payment')
	 	->returnUrl('http://www.test.nl/success')
	 	->cancelUrl('http://www.test.nl/canceled')
	 	->reportUrl('http://www.test.nl/report');

	if ($method=="Ideal") { $startPayment->bank('0021'); }
	if ($method=="Sofort") { $startPayment->country(49); }

	$startPaymentResult = $startPayment->start();

	// Succesful payment start

	if ($startPaymentResult->status) {

		// Check payment
		echo $startPaymentResult->url." => ";

		$checkPaymentResult = TargetPay\Transaction::model($method)
		 	->rtlo(121455)
		 	->txid($startPaymentResult->txid)
		 	->test(true)
		 	->check();

		echo ($checkPaymentResult->status) ? "OK" : "fail";
		echo "\n";

	} else {

		// Unsuccesful
		echo $startPaymentResult->error;
		echo "\n";
	}
}

test ("Ideal");
test ("MisterCash");
test ("Sofort");
test ("Creditcard");
test ("Paysafecard");
