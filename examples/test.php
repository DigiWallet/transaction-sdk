#!/usr/bin/env php
<?php

require_once __DIR__ . '/../lib/DigiWallet/autoload.php';

/**
 *  Function to test DigiWallet calls for the various payment methods
 *  Test parameters
 *  - outletId              143835 (change this and make sure your outlet is running in test-mode!)
 *  - description           Test payment
 *  - bank for iDEAL        RABONL2U (Rabobank)
 *  - country for Sofort    DE (Germany)
 *  - ...
 *
 *  After payment start, the payment is checked
 */
function test($method)
{
    echo "------------------------------------------------------------------------------\n";
    echo "$method : ";

    // Start payment

    $startPayment = DigiWallet\Transaction::model($method)
        ->outletId(143835)
        ->amount(1000)
        ->description('Test payment')
        ->returnUrl('http://www.test.nl/success')
        ->cancelUrl('http://www.test.nl/canceled')
        ->reportUrl('http://www.test.nl/report');

    if ($method == "Ideal") {
        $startPayment->bank('RABONL2U');
    }
    if ($method == "Sofort") {
        $startPayment->country('DE');
    }

    $startPaymentResult = $startPayment->start();

    if ($startPaymentResult->status) {
        // Successful payment start, call check API
        echo $startPaymentResult->url . " => ";

        $checkPaymentResult = DigiWallet\Transaction::model($method)
            ->outletId(143835)
            ->transactionId($startPaymentResult->transactionId)
            ->check();

        echo ($checkPaymentResult->status) ? "OK" : "fail";
        echo "\n";
    } else {
        // Unsuccessful
        echo $startPaymentResult->error;
        echo "\n";
    }
}

test("Ideal");
test("Bancontact");
test("Sofort");
test("Creditcard");
test("Paysafecard");
