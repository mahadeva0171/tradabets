<?php

namespace App\Http\Controllers;

use App\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use MongoDB\Driver\Session;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use App\Helpers\PaymentHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Redirect;
use Paystack;

class PaymentController extends Controller
{
    //
    public function __construct()
    {
        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }
    
    public function payment(Request $request){
        $deposit_amt=$request->get('deposit_amount');
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();
        $item_1->setName('Item 1') /** item name **/
        ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice( $deposit_amt); /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal( $deposit_amt);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('deposit-request')) /** Specify return URL **/
        ->setCancelUrl(URL::route('deposit-request'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/

        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
                $request->session()->put('error', 'Connection timeout');
                return Redirect::route('payment-request');
            } else {
                $request->session()->put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::route('payment-request');
            }
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        /** add payment ID to session **/
        $request->session()->put('amount',$deposit_amt);

        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return redirect($redirect_url);
        }
        $request->session()->put('error', 'Unknown error occurred');
        return redirect('payment-request');
    }

    public function depositAmount(Request $request)
    {
        $payment_id=$_GET['paymentId'];
        $amount=$request->session()->get('amount');
        $avail_balance=0.0;

        if (empty($_GET['PayerID']) || empty($_GET['token']) ) {
            $request->session()->put('error','Payment Failed');
            return redirect('deposit-form');
        }
        $payment=Payment::get($payment_id,$this->_api_context);
        $execution=new PaymentExecution();
        $execution->setPayerId($_GET['PayerID']);

        $result=$payment->execute($execution, $this->_api_context);
        
        if($result->getState()=='approved'){
            $user=auth()->user();
            PaymentHelper::create_transaction($amount,$user->id,'deposit');
            $transaction_check = \App\Models\Transaction::where('user_id',$user->id)->get()->all();
            $transaction_count=count($transaction_check);
            if($transaction_count == 1)
            {
                PaymentHelper::create_transaction($amount,$user->id,'bonus');
            }
            $request->session()->put('success','Payment success');
            return redirect('deposits');
        }
        $request->session()->put('error','Payment Failed');
            return redirect('deposits');

        //$balance->update($form_bal);

    }


    /********      PAYSTACK
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {

        $paystack = new Paystack();
        $user = auth()->user();
        $request->email = $user->email;
        // $request->amount = $amount;
              
        $request->reference = PaymentHelper::initiate_transaction($user->id, 'request', $request->amount);
        try {
        
            return Paystack::getAuthorizationUrl()->redirectNow();
        } catch (\Exception $e) {
            return Redirect::back()->withMessage(['msg' => 'The paystack token has expired. Please refresh the page and try again.', 'type' => 'error']);
        }
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback(Request $request)
    {
        $paymentDetails = Paystack::getPaymentData();

       // dd($paymentDetails);

        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want

        $amount = ( ($paymentDetails ['data']['amount']) / 100 );
        $status = ($paymentDetails ['status']);
        $reference = ($paymentDetails['data']['reference']);

        if ($status == 'true') {
            $user_id = PaymentHelper::get_user_id_by_reference($reference);
            PaymentHelper::update_transaction($amount, $user_id, $reference, 'deposit');

            $transaction_check = \App\Models\Transaction::where('user_id', $user_id)->get()->all();
            $transaction_count = count($transaction_check);
            if ($transaction_count == 1) {
                PaymentHelper::create_transaction($amount, $user_id, 'bonus');
            }

//            $status->session()->put('success', 'Payment success');
            return redirect('deposits');
        }
        $status->session()->put('error', 'Payment Failed');
        return redirect('deposits');
    }

}
