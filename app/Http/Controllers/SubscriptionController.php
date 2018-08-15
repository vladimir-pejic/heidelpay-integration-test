<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Heidelpay\PhpPaymentApi\Exceptions\UndefinedTransactionModeException;
use Heidelpay\PhpPaymentApi\PaymentMethods\CreditCardPaymentMethod;
use Heidelpay\PhpPaymentApi\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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


    public function cc() {

        define('HEIDELPAY_PHP_PAYMENT_API_EXAMPLES', true);
        define('HEIDELPAY_PHP_PAYMENT_API_URL', 'http://'.$_SERVER["HTTP_HOST"]).'/cc';
//        define('HEIDELPAY_PHP_PAYMENT_API_URL', 'https://dev.heidelpay.de');
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
//            'http://'.$_SERVER["HTTP_HOST"] . '/vendor/heidelpay/php-payment-api/example/' . 'HeidelpayResponse.php'  // Response url from your application
            'https://dev.heidelpay.de/HeidelpayResponse.php'
        );

        /**
         * Set up customer information required for risk checks
         */
        $cc->getRequest()->customerAddress(
            'dasdad',                  // Given name
            'TESTER',         // Family name
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
//        $cc->registration(
//            HEIDELPAY_PHP_PAYMENT_API_URL, // PaymentFrameOrigin - uri of your application like https://dev.heidelpay.com
//            'TRUE', // PreventAsyncRedirect - this will tell the payment weather it should redirect the customer or not
//            HEIDELPAY_PHP_PAYMENT_API_URL . HEIDELPAY_PHP_PAYMENT_API_FOLDER . 'style.css'   // CSSPath - css url to style the Heidelpay payment frame
//        );

        $cc->registration(
            "http://127.0.0.1:9000", //uri of your application
            "FALSE", //PreventAsyncRedirect
            "https://dev.heidelpay.de/style.css" //CSSPath
        );

        return view('cciframe')->with(compact('cc'));

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

//        echo '<pre>';
//        print_r($cc);
//        echo '</pre>';
//        die();

//        return $cc->getResponse()->getPaymentFormUrl();

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
}
