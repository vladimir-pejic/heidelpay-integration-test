<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Heidelpay\PhpPaymentApi\Exceptions\UndefinedTransactionModeException;
use Heidelpay\PhpPaymentApi\PaymentMethods\CreditCardPaymentMethod;
use Heidelpay\PhpPaymentApi\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Omnipay\Omnipay;

class SubscriptionController extends Controller
{
    //
    public function store(Request $request, $subscription_id) {
        $user = Auth::user();
        $user->subscription_id = $subscription_id;
        $user->subscription_date = Carbon::now()->format('y-m-d');
        $user->save();
        return redirect()->back();
    }

    public function cancel() {
        $user = Auth::user();
        $user->subscription_id = null;
        $user->subscription_date = null;
        $user->save();
        return redirect()->back();
    }




    // VANILLA HEIDELPAY IFRAME CALL
    public function cciframe() {

        define('HEIDELPAY_PHP_PAYMENT_API_EXAMPLES', true);
        define('HEIDELPAY_PHP_PAYMENT_API_URL', 'https://dev.heidelpay.de');
        define('HEIDELPAY_PHP_PAYMENT_API_FOLDER', '/vendor/heidelpay/php-payment-api/example/');

        $cc = new CreditCardPaymentMethod();

        /**
         * Set up your authentification data for Heidepay api
         *
         * @link https://dev.heidelpay.com/testumgebung/#Authentifizierungsdaten
         */
        $cc->getRequest()->authentification(
            '31HA07BC8142C5A171745D00AD63D182',  // SecuritySender
            '31ha07bc8142c5a171744e5aef11ffd3',  // UserLogin
            '93167DE7',                          // UserPassword
            '31HA07BC8142C5A171744F3D6D155865',  // TransactionChannel credit card without 3d secure
            true                                 // Enable sandbox mode
        );
        /**
         * Set up asynchronous request parameters
         */
        $cc->getRequest()->async(
            'EN', // Language code for the Frame
            'https://experiments.kompitenz.de/cciframe/response'
        );

        /**
         * Set up customer information required for risk checks
         */
        $cc->getRequest()->customerAddress(
            'Kompitenz',                  // Given name
            'Test',         // Family name
            null,                   // Company Name
            '12344',                   // Customer id of your application
            'Vagerowstr. 18',       // Billing address street
            'DE-BW',                   // Billing address state
            '69115',                   // Billing address post code
            'Heidelberg',              // Billing address city
            'DE',                      // Billing address country code
            'support@heidelpay.com'     // Customer mail address
        );

        /**
         * Set up basket or transaction information
         */
        $cc->getRequest()->basketData(
            '2843294932',                   // Reference Id of your application
            100.00,                         // Amount of this request
            'EUR',                         // Currency code of this request
            '39542395235ßfsokkspreipsr'    // A secret passphrase from your application
        );

        /**
         * Set necessary parameters for Heidelpay payment Frame and send a registration request
         */
        $cc->registration(
            "https://experiments.kompitenz.de/cciframe", //uri of your application
            "FALSE", //PreventAsyncRedirect
            "https://dev.heidelpay.de/style.css" //CSSPath
        );

        return view('cciframe')->with(compact('cc'));

    }


    // Response for stock Heidelpay iframe
    public function cciframeResponse(Request $request) {
        $heidelpayResponse = new \Heidelpay\PhpPaymentApi\Response($request->all());
        return $heidelpayResponse;
    }




    public function ccCallback(Request $request) {

        $heidelpayResponse = new Response($_POST);

        if($heidelpayResponse->isSuccess())
            return 'success';
        else if($heidelpayResponse->isError())
            return 'error';
        else
            return 'no reposnse at all dude.';

    }

    public function ccPost(Request $request) {

        $auth = [
            '31HA07BC8142C5A171745D00AD63D182',  // SecuritySender
            '31ha07bc8142c5a171744e5aef11ffd3',  // UserLogin
            '93167DE7',                          // UserPassword
            '31HA07BC8142C5A171744F3D6D155865',  // TransactionChannel credit card without 3d secure
            true                                 // Sandbox or not
        ];

        $basket = [
            '1234567890',                   // Reference Id from application
            100.00,                         // Amount of this request
            'EUR',                          // Currency code of this request
            '39542395235ßfsokkspreipsr'     // A secret pass phrase from application
        ];

        $customer = [
            'Vladimir',                     // First name
            'Pejic',                        // Last name
            null,                           // Company Name
            '12344',                        // Customer id of your application
            'Vlasicka 27A',                 // Billing address street
            'RS',                           // Billing address state
            '78000',                        // Billing address post code
            'Banja Luka',                   // Billing address city
            'BA',                           // Billing address country code
            'vladimir.pejic@gmail.com'      // Customer mail address
        ];

        $heidelpayResponse = new \Heidelpay\PhpPaymentApi\Response($_POST);
        $paymentReference = $heidelpayResponse->getPaymentReferenceId();

        $cc = new \Heidelpay\PhpPaymentApi\PaymentMethods\CreditCardPaymentMethod();
        $cc->getRequest()->authentification(...$auth);
        $cc->getRequest()->basketData(...$basket);
        $cc->getRequest()->customerAddress(...$customer);
        $cc->getRequest()->getAccount()->setBrand($request->brand);
        $cc->getRequest()->getAccount()->setExpiryMonth($request->expiry_month);
        $cc->getRequest()->getAccount()->setExpiryYear($request->expiry_year);
        $cc->getRequest()->getAccount()->setHolder($request->holder);
        $cc->getRequest()->getAccount()->setNumber($request->number);
        $cc->getRequest()->getAccount()->setVerification($request->verification);


        $cc->registration(
            HEIDELPAY_PHP_PAYMENT_API_URL,
            // PaymentFrameOrigin - uri of your application like https://dev.heidelpay.com
            'FALSE',
            // PreventAsyncRedirect - this will tell the payment weather it should redirect the customer or not
            HEIDELPAY_PHP_PAYMENT_API_URL .
            HEIDELPAY_PHP_PAYMENT_API_FOLDER .
            'style.css'   // CSSPath - css url to style the Heidelpay payment frame
        );


        try {
            $cc->capture($paymentReference);
        } catch (UndefinedTransactionModeException $e) {
            return $e->getMessage();
        }

        if($cc->getResponse()->isSuccess()) {
            return $cc->getResponse()->getPaymentReferenceId();
        } else {
            return 'nema successa';
        }
        if ($cc->getResponse()->isError()) {
            print_r($cc->getResponse()->getError());
        } else {
            return 'nema ni pravog errora';
        }
    }

    public function wirecardGet() {

        $card = [
            'firstName' => 'Vladimir',
            'lastName' => 'Testing',
            'number',
            'expiryMonth',
            'expiryYear',
            'startMonth',
            'startYear',
            'cvv',
            'issueNumber',
            'type',
            'billingAddress1',
            'billingAddress2',
            'billingCity',
            'billingPostcode',
            'billingState',
            'billingCountry',
            'billingPhone',
            'shippingAddress1',
            'shippingAddress2',
            'shippingCity',
            'shippingPostcode',
            'shippingState',
            'shippingCountry',
            'shippingPhone',
            'company',
            'email'
        ];


        $gateway = Omnipay::create('Wirecard_CheckoutPage');

        // This customer ID invokes demo mode. Try credit card MC: 9500000000000002
        $gateway->setCustomerId('D200001');
        $gateway->setSecret('B8AKTPWBRMNBV455FG6M2DANE99WU2');

        // Because failureUrl and serviceUrl are gateway-specific, they can also be set
        // as gateway configuration options:
        $gateway->setFailureUrl('https://example.com/complete?status=failure');
        $gateway->setServiceUrl('https://example.com/terms_of_service_and_contact');

        // Most other gateway and API-specific parameters (i.e. those not recognised by
        // the Omnipay core) can be set at the gateway or the message level.

        $request = $gateway->purchase([
            'transactionId' => 31313131313, // merchant site generated ID
            'amount' => "9.00",
            'currency' => 'EUR',
            'invoiceId' => 'FOOOO',
            'description' => 'An order',
            'paymentType' => 'CCARD',
            'card' => $card, // billing and shipping details
            //'items' => $items, // array or ItemBag of Omnipay\Common\Item or Omnipay\Wirecard\Extend\Item objects

            // These three URLs are required to the gateway, but will be defaulted to the
            // returnUrl where they are not set.
            'returnUrl' => 'https://example.com/wirecard/response',
            //'cancelUrl' => 'https://example.com/complete?status=cancel', // User cancelled
            //'failureUrl' => 'https://example.com/complete?status=failure', // Failed to authorise
            //
            // These two URLs are required.
            'notifyUrl' => 'https://example.com/acceptNotification',
            'serviceUrl' => 'https://example.com/terms_of_service_and_contact',
            //
            'confirmMail' => 'vladimir.pejic@gmail.com',
        ]);
        $response = $request->send();

        // Quick and dirty way to POST to the gateway, to get to the
        // remote hosted payment form.
        // This is ignoring error checking, as detailed in the Omnipay documentation.
        echo $response->getRedirectResponse();
        exit;

    }


    public function wirecardPost(Request $request) {

        $url = 'https://api-test.wirecard.com/engine/rest/paymentmethods/'; // XML or JSON *test* endpoint
        $merchant_ref = 'payment' . rand(11111,99999) . '-' . time() . '-' . rand(1000,10000);


        $xml_request = '<?xml version="1.0" encoding="utf-8"?>
                        <payment xmlns="http://www.elastic-payments.com/schema/payment">
                           <merchant-account-id>07edc10b-d3f9-4d12-901f-0db7f4c7e75c</merchant-account-id>
                           <request-id>'.$merchant_ref.'</request-id>
                           <transaction-type>purchase</transaction-type>
                           <requested-amount currency="USD">1.01</requested-amount>
                           <account-holder>
                              <first-name>Vladimir</first-name>
                              <last-name>Testing</last-name>
                              <email>john.doe@test.com</email>
                              <phone>+1(416)1112222</phone>
                              <address>
                                 <street1>500.1053</street1>
                                 <city>Brantford</city>
                                 <state>ON</state>
                                 <country>CA</country>
                              </address>
                           </account-holder>
                           <card>
                              <account-number>4012000300001003</account-number>
                              <expiration-month>01</expiration-month>
                              <expiration-year>2019</expiration-year>
                              <card-type>visa</card-type>
                              <card-security-code>003</card-security-code>
                           </card>
                           <ip-address>127.0.0.1</ip-address>
                           <payment-methods>
                              <payment-method name="creditcard" />
                           </payment-methods>
                        </payment>';

        $json_request = '{
                          "payment" : {
                            "merchant-account-id" : {
                              "value" : "9105bb4f-ae68-4768-9c3b-3eda968f57ea"
                            },
                            "request-id" : "fb78df4a-9784-4fea-bd3c-1038e031ad56",
                            "transaction-type" : "purchase",
                            "requested-amount" : {
                              "value" : 1.05,
                              "currency" : "EUR"
                            },
                            "account-holder" : {
                              "first-name" : "Vladimir",
                              "last-name" : "Doe",
                              "email" : "john.doe@test.com",
                              "phone" : "+1(1)4161234567",
                              "address" : {
                                "street1" : "123 kkkkkkkkk",
                                "city" : "Brantford",
                                "state" : "ON",
                                "country" : "CA"
                              }
                            },
                            "card" : {
                              "account-number" : "4012000300001003 ",
                              "expiration-month" : 01,
                              "expiration-year" : 2019,
                              "card-security-code" : "003",
                              "card-type" : "visa"
                            },
                            "ip-address" : "127.0.0.1",
                            "payment-methods" : {
                              "payment-method" : [ {
                                "name" : "creditcard"
                              } ]
                            }
                          }
                        }';

        $ch = curl_init();
        $headers = array(
            'Content-Type: application/xml',
            'Authorization: Basic '. base64_encode("70000-APILUHN-CARD:8mhwavKVb91T")
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_request);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $results = curl_exec($ch);
        curl_close($ch);

        return dd(simplexml_load_string($results));

    }
}
