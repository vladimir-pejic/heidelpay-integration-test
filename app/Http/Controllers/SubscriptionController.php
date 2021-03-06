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

        $timestamp = Carbon::now()->format('Ymdhis');
        $merchant_id = '5612f2ca-344e-41cc-8ed1-be1523c38182'; // MBP Merchant ID
        $merchant_secret = '61c14fec-3e06-4563-9574-c75b209d10fb'; // MBP secret key
        $merchant_ref = rand(11111,99999) . '-' . time() . '-' . rand(1000,10000);
        $type = 'purchase';
        $amount = '1.00';
        $currency = 'EUR';


        /*
         * Object to be sent on frontend request, via deprecated method.
         */
//        $request_signature = hash('sha256', $timestamp.$merchant_ref.$merchant_id.$type.$amount.$currency.$merchant_secret);
//
//        $requestData = json_encode([
//            "request_id" => $merchant_ref,
//            "request_time_stamp" => $timestamp,
//            "merchant_account_id" => $merchant_id,
//            "transaction_type" => $type,
//            "requested_amount" => $amount,
//            "requested_amount_currency" => $currency,
//            "ip_address" => "127.0.0.1",
//            "request_signature" => $request_signature,
//            "payment_method" => "creditcard"
//        ]);


        /*
         * Based on documentation, this test request works.
         */
         $pass = base64_encode('meinbuch_2018!:788bhv3wMc4');
        $test_merchant_id = '7a6dd74f-06ab-4f3f-a864-adc52687270a'; // Test Merchant ID
        $request_url = 'https://wpp.wirecard.com/api/payment/register';
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $pass
        );

        // via documentation (seamless)
        $request_json = json_encode([
            "payment" => [
                "merchant-account-id" => [
                    "value" => $merchant_id
                ],
                "request-id" => $merchant_ref,
                "transaction-type" => "authorization",
                "requested-amount" => [
                    "value" => 0.1,
                    "currency" => "EUR"
                ],
                "account-holder" => [
                   "first-name" => "Vladimir",
                   "last-name" => "Pejic",
                ],
                "payment-methods" => [
                    "payment-method" => [
                        [
                            "name" => "creditcard"
                        ]
                    ]
                ],
            ],
            "options" => [
                "mode" => "seamless",
                "frame-ancestor" => url('/')
            ]
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_json);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $string = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($string, true);


        return view('wirecard_form')->with(compact('result'));

    }


    public function wirecardPost(Request $request) {

//        $url = 'https://api-test.wirecard.com/engine/rest/paymentmethods/'; // XML or JSON *test* endpoint
//        $merchant_id = '07edc10b-d3f9-4d12-901f-0db7f4c7e75c'; // Test Merchant ID

        $url = 'https://api.wirecard.com/engine/rest/paymentmethods/'; // XML or JSON endpoint
        $merchant_id = '5612f2ca-344e-41cc-8ed1-be1523c38182';

        $merchant_ref = rand(11111,99999) . '-' . time() . '-' . rand(1000,10000);

        $xml_request = '<?xml version="1.0" encoding="utf-8"?>
                        <payment xmlns="http://www.elastic-payments.com/schema/payment">
                           <merchant-account-id>'.$merchant_id.'</merchant-account-id>
                           <request-id>'.$merchant_ref.'</request-id>
                           <transaction-type>purchase</transaction-type>
                           <requested-amount currency="EUR">'.$request->amount.'</requested-amount>
                           <account-holder>
                              <first-name>'.$request->first_name.'</first-name>
                              <last-name>'.$request->last_name.'</last-name>
                              <email>'.$request->email.'</email>
                              <phone>'.$request->phone.'</phone>
                              <address>
                                 <street1>'.$request->street_address.'</street1>
                                 <city>'.$request->city.'</city>
                                 <country>ba</country>
                              </address>
                           </account-holder>
                           <card>
                              <account-number>'.$request->card.'</account-number>
                              <expiration-month>'.$request->expiry_month.'</expiration-month>
                              <expiration-year>'.$request->expiry_year.'</expiration-year>
                              <card-type>visa</card-type>
                              <card-security-code>'.$request->cvv.'</card-security-code>
                           </card>
                           <ip-address>'.$request->ip().'</ip-address>
                           <payment-methods>
                              <payment-method name="creditcard" />
                           </payment-methods>
                        </payment>';

        $ch = curl_init();
        $headers = array(
            'Content-Type: application/xml',
            'Authorization: Basic '. base64_encode("meinbuch_2018!:788bhv3wMc4")
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
