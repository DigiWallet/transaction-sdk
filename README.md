Targetpay SDK
=========

With this (namespaced) SDK you can easily make the calls to TargetPay for all the payment methods that are provided.

Required: PHP 5.3+

Installation
============
- Copy the files in /lib to your webserver. 

Usage
=====
First register the autoloader. If your framework already has an autoloader you might not need to use this. 
Use the proper route to the autoload file

<pre>require_once __DIR__.'/../lib/TargetPay/autoload.php';</pre>

Include the TargetPay namespace:

<pre>use \TargetPay as TargetPay;</pre>

Now start the payment by calling:

<pre>
$startPaymentResult = TargetPay\Transaction::model("Ideal")
    ->rtlo(121455)
    ->amount(1000)
    ->description('Test payment')
    ->returnUrl('http://www.test.nl/success')
    ->cancelUrl('http://www.test.nl/canceled')
    ->reportUrl('http://www.test.nl/report')
    ->bank('0021')
    ->start();	 	
</pre>

You can check the result. <code>$startPaymentResult->status</code> will be true in case of success. If not <code>startPaymentResult->status</code> will have the error message.

If all is OK, redirect to the bank:

<pre>
Header ("Location: ".$startPaymentResult->url);
</pre>

After payment the reportUrl will be called by TargetPay, passing amongst others the transaction ID . See the TargetPay documentation for more information.

The script behind this report URL has to check the validity of the report, thus checking the actual payment. 

This is done by calling (after including the autoloader and use statement):

<pre>
		$checkPaymentResult = TargetPay\Transaction::model($method)
		 	->rtlo(121455)
		 	->txid($startPaymentResult->txid)
		 	->test(false)
		 	->check();
</pre>
		 	
Check the results in <code>$checkPaymentResult</code> for more information about the payment. Most important: <code>$checkPaymentResult = true</code> indicates a succesful payment. 
Save the result of the payment in your database. 

When the customer paid, he will be redirected to the Return URL. If not, he is redirected  to the Cancel URL. 


Payment methods
==========================
Currently the following payment methods are implemented. See their classes (in parentheses) under /lib/TargetPay/Methods for their specifics like minimum/maximum amounts and specific properties:
- iDEAL (Methods_Ideal)
- Bancontact/Mister Cash (Methods_MisterCash)
- Sofort Banking, former DirectEbanking (Methods_Sofort)
- Paysafecard, former Wallie (Methods_Paysafecard)
- Visa/Mastercard (Methods_Creditcard)

Notes
=====
- The initial call described above is slightly simplified. For iDEAL you first need to ask the customer to select his bank and pass it with bank(). For Sofort you specify country(), the options are listed in the documentation. Other payment methods do not need these parameters.
- Do not forget to enter your own layoutcode. 121455 is just a testcode. 






