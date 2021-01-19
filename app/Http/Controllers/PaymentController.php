<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use Config;

class PaymentController extends Controller
{
    private $apiContext;

    public function __construct()
    {
        $payPalConfig = Config::get('paypal');
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $payPalConfig['client_id'],
                $payPalConfig['secret']
            )
        );
        $this->apiContext->setConfig($payPalConfig['settings']);
    }

    public function payWithPayPal()
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $amount = new Amount();
        $amount->setTotal('3.99');
        $amount->setCurrency('USD');
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $callbackUrl = url('/paypal/status');
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($callbackUrl)
            ->setCancelUrl($callbackUrl);
        $payment = new Payment();
        $payment->setIntent('sale')
        ->setPayer($payer)
        ->setTransactions(array($transaction))
        ->setRedirectUrls($redirectUrls);
        try {
            $payment->create($this->apiContext);
            return redirect()->away($payment->getApprovalLink());
        } catch (PayPalConnectionException $ex) {
            echo $ex->getData();
        }
    }

    public function payPalStatus(Request $request)
    {
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');
        $token = $request->input('token');
        if (!$paymentId || !$payerId || !$token) {
            $status = 'We are sorry! We are not able to process your request right now.';
            return redirect('/paypal/failed')->with(compact('status'));
        }
        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);
        /** Execute the payment **/
        $result = $payment->execute($execution, $this->apiContext);
        if ($result->getState() === 'approved') {
            $status = 'Thank you! Transaction was successful!';
            return redirect('/results')->with(compact('status'));
        }
        $status = 'We are sorry! We are not able to process your request right now.';
        return redirect('/results')->with(compact('status'));
    }
}
