DigiWallet SDK
=========

With this (namespaced) SDK you can easily make the calls to DigiWallet for all the payment methods that are provided.

Required: PHP 5.4+

Installation
============
- Copy the files in /lib to your webserver. 

Usage
=====
First register the autoloader. If your framework already has an autoloader you might not need to use this. 
Use the proper route to the autoload file

<pre>require_once __DIR__.'/../lib/DigiWallet/autoload.php';</pre>

Include the DigiWallet namespace:

<pre>use \DigiWallet as DigiWallet;</pre>

Now start the payment by calling:

<pre>
$startPaymentResult = DigiWallet\Transaction::model("Ideal")
    ->outletId(143835)
    ->amount(1000)
    ->description('Test payment')
    ->returnUrl('http://www.test.nl/success')
    ->cancelUrl('http://www.test.nl/canceled')
    ->reportUrl('http://www.test.nl/report')
    ->bank('INGBNL2A')
    ->start();	 	
</pre>

You can check the result. <code>$startPaymentResult->status</code> will be true in case of success. If not <code>startPaymentResult->status</code> will have the error message.

If all is OK, redirect to the bank:

<pre>
Header ("Location: ".$startPaymentResult->url);
</pre>

After payment the reportUrl will be called by DigiWallet, passing amongst others the transaction ID . See the DigiWallet documentation for more information.

The script behind this report URL has to check the validity of the report, thus checking the actual payment. 

This is done by calling (after including the autoloader and use statement):

<pre>
		$checkPaymentResult = DigiWallet\Transaction::model($method)
		 	->outletId(143835)
		 	->transactionId($startPaymentResult->transactionId)
		 	->check();
</pre>
		 	
Check the results in <code>$checkPaymentResult</code> for more information about the payment. Most important: <code>$checkPaymentResult = true</code> indicates a succesful payment. 
Save the result of the payment in your database. 

When the customer paid, he will be redirected to the Return URL. If not, he is redirected  to the Cancel URL. 

Test-mode
==========================
When logged in to our Organization Dashboard in DigiWallet, you have the option to enable Test-mode for any of our outlets.
If you do this, every transaction started on that outlet will immediately run in test mode, meaning no actual money will be transferred.

Instead of the payment method's specific banking environment, a link to the DigiWallet Transaction Test Panel is returned.
On this panel you can very easily manipulate the status of the transaction, to simulate a successful customer return or server-to-server callback, or to simulate cancellations or errors.

Payment methods
==========================
Currently the following payment methods are implemented. See their classes (in parentheses) under /lib/DigiWallet/Methods for their specifics like minimum/maximum amounts and specific properties:
- iDEAL (Ideal)
- Bancontact/Mister Cash (Bancontact)
- Sofort Banking (Sofort)
- Paysafecard (PaysafeCard)
- PayPal (Paypal)
- Visa/Mastercard/AMEX (Creditcard)

Notes
=====
- Do not forget to enter your own outletId. 143835 is just a demo-outlet that is always running in Test-mode. 






