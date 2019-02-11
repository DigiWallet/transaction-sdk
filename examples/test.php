#!/usr/bin/env php
<?php

use DigiWallet\Methods\Afterpay;
use DigiWallet\Methods\Ideal;
use DigiWallet\Methods\Sofort;

require_once __DIR__ . '/../lib/DigiWallet/autoload.php';

/**
 *  Function to test DigiWallet calls for the various payment methods
 *  Test parameters
 *  - outletId              143835 (change this to your own outlet)
 *      By default these transactions are forced into test-mode, either through the outlet being in test-mode or
 *      because of the ->test(true) method call. You can adjust accordingly to test for yourself.
 *  - description           Test payment
 *  - bank for iDEAL        RABONL2U (Rabobank)
 *  - country for Sofort    DE (Germany)
 *  - ...
 *
 *  After payment start, the payment is checked
 */
function test($method)
{
    echo '------------------------------------------------------------------------------' . PHP_EOL;
    echo $method . ':' . PHP_EOL;

    // Start payment

    $startPayment = DigiWallet\Transaction::model($method)
        ->outletId(143835)
        ->amount(1000)
        ->description('Test payment')
        ->returnUrl('http://www.test.nl/success')
        ->cancelUrl('http://www.test.nl/canceled')
        ->reportUrl('http://www.test.nl/report')
        ->test(true);

    if ($method === 'Ideal') {
        addIdealSpecificParameters($startPayment);
    }
    if ($method === 'Sofort') {
        addSofortSpecificParameters($startPayment);
    }
    if ($method === 'Afterpay') {
        addAfterpaySpecificParameters($startPayment);
    }

    $startPaymentResult = $startPayment->start();

    if ($startPaymentResult->status) {
        // Successful payment start, call check API
        echo 'Start Response: ' . $startPaymentResult->url . PHP_EOL;

        $checkPaymentResult = DigiWallet\Transaction::model($method)
            ->outletId(143835)
            ->transactionId($startPaymentResult->transactionId)
            ->test(true)
            ->check();

        echo 'Check Response: ' . ($checkPaymentResult->status ? 'OK' : $checkPaymentResult->error) . PHP_EOL;
    } else {
        // Unsuccessful
        echo 'Start Response: ' . $startPaymentResult->error . PHP_EOL;
    }
}

/**
 * @param $startPayment Ideal
 * @return Ideal
 */
function addIdealSpecificParameters($startPayment)
{
    return $startPayment->bank('RABONL2U');
}

/**
 * @param $startPayment Sofort
 * @return Sofort
 */
function addSofortSpecificParameters($startPayment)
{
    return $startPayment->country('DE');
}

/**
 * @param $startPayment Afterpay
 * @return Afterpay
 */
function addAfterpaySpecificParameters($startPayment)
{
    return $startPayment->invoiceLines([
            0 => [
                'productCode' => '0001-TEST',
                'productDescription' => 'Test Object 1',
                'quantity' => 1,
                'price' => 10.00,
                'taxCategory' => 1,
            ]
        ])
        ->billingStreet('Dorpsstraat')
        ->billingHouseNumber('1')
        ->billingPostalCode('1234AB')
        ->billingCity('Duckstad')
        ->billingPersonEmail('miep@example.com')
        // Normally not known in a regular webshop, leave out to force the enrichment screen ->billingPersonInitials('M.B.')
        // Normally not known in a regular webshop, leave out to force the enrichment screen ->billingPersonGender('F')
        ->billingPersonSurname('Modaal')
        ->billingCountryCode('NLD')
        ->billingPersonLanguageCode('NLD')
        // Normally not known in a regular webshop, leave out to force the enrichment screen ->billingPersonBirthDate('1985-12-05')
        ->billingPersonPhoneNumber('+31612345678')
        ->shippingStreet('Dorpsstraat')
        ->shippingHouseNumber('1')
        ->shippingPostalCode('1234AB')
        ->shippingCity('Duckstad')
        ->shippingPersonEmail('miep@example.com')
        // Normally not known in a regular webshop, leave out to force the enrichment screen ->shippingPersonInitials('M.B.')
        // Normally not known in a regular webshop, leave out to force the enrichment screen ->shippingPersonGender('F')
        ->shippingPersonSurname('Modaal')
        ->shippingCountryCode('NLD')
        ->shippingPersonLanguageCode('NLD')
        // Normally not known in a regular webshop, leave out to force the enrichment screen ->shippingPersonBirthDate('1985-12-05')
        ->shippingPersonPhoneNumber('+31612345678');
}

test('Ideal');
test('Bancontact');
test('Sofort');
test('Creditcard');
test('Paysafecard');
test('Paypal');
test('Afterpay');
