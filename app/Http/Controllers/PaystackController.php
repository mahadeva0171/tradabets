<?php
namespace Unicodeveloper\Paystack;
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\UserBankDetails;
use App\PaymentReport;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class PaystackController extends Controller
{
     /**
     * Issue Secret Key from your Paystack Dashboard
     * @var string
     */
    protected $secretKey;

    /**
     * Paystack API base Url
     * @var string
     */
    protected $baseUrl;

    /**
     * Authorization Url - Paystack payment page
     * @var string
     */
    protected $authBearer;
    //
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->setKey();
        $this->setBaseUrl();
        $this->setRequestOptions();
    }

    /**
     * Get Base Url from Paystack config file
     */
    public function setBaseUrl()
    {
        $this->baseUrl = Config::get('paystack.paymentUrl');
    }

    /**
     * Get secret key from Paystack config file
     */
    public function setKey()
    {
        $this->secretKey = Config::get('paystack.secretKey');
    }

    /**
     * Set options for making the Client request
     */
    private function setRequestOptions()
    {
        $this->authBearer = array(
              "Authorization: Bearer " . $this->secretKey,
              "Cache-Control: no-cache",
              );
    }

    public function initiate(Request $request, $id)
    {
        $user = DB::table('withdraw_requests')
            ->select('user_id','amount')
            ->where('id', $id)
            ->first();

        // $recipient_code = DB::table('user_bank_accounts')
        //     ->select('recipient_code')
        //     ->where(['user_id', $user->user_id],['Active_status',"Active"]);

        // $recipient_code = DB::table('user_bank_accounts')
        //     ->where('user_id', $user->user_id)
        //     ->where('Active_status',"Active")
        //     ->select('recipient_code');

        $recipient_code = DB::table('user_bank_accounts')
            ->where('user_id', $user->user_id)
            ->where('Active_status',"Active")
            ->value('recipient_code');

        $amount = round($user->amount * 100);

        $reason = "Withdrawal transfers";

              $url = $this->baseUrl . "/transfer";
              $fields = [
                'source' => "balance",
                'amount' => $amount,
                'recipient' => $recipient_code,
                'reason' => $reason
              ];
              // return $recipient_code;
              $fields_string = http_build_query($fields);

              //open connection
              $ch = curl_init();
              
              //set the url, number of POST vars, POST data
              curl_setopt($ch,CURLOPT_URL, $url);
              curl_setopt($ch,CURLOPT_POST, true);
              curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
              curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authBearer);
              
              //So that curl_exec returns the contents of the cURL; rather than echoing it
              curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
              
              //execute post
              $result = curl_exec($ch);
              // echo $recipient_code;

              $initiate = json_decode($result);
              // echo $result;
               $status = $initiate->status;
               $message = $initiate->message;
               // $transfer_status1 = $initiate->data->status;

              if ($status == true) {

                  $message = $initiate->data->status;
                  $reference = $initiate->data->reference;
                  $amount = ($initiate->data->amount / 100) ;
                    // $request->has('spid_id') ? $request->input('spid_id') : NULL,
                  $reason = $initiate->data->reason;
                  $transfer_code = $initiate->data->transfer_code;
                  $createdAt = $initiate->data->createdAt;
                  $transaction_status = $initiate->data->status;

                    // $values = array('transaction_reference' => $reference, 'amount' => $amount, 'status' => $transaction_status, 'transaction_code' => $transfer_code, 'payment_at' => $createdAt, 'user_id' => $user->user_id, 'recipient_code' => $recipient_code);

                    // $query =  DB::table('payment_transaction_report')->insert($values);

                    $query = PaymentReport::create(['transaction_reference'=>$reference,
                            'amount'=> $amount,
                            'status'=> $transaction_status,
                            'transaction_code'=> $transfer_code,
                            'payment_at'=> $createdAt,
                            'user_id'=>$user->user_id,
                            'recipient_code'=>$recipient_code,
                        ]);

                    $update = DB::table('withdraw_requests')
                        ->where('id', $id)
                        ->update(['status' => "approved"]);

                    if (!$query) {
                        return redirect('/withdraw-requests')->with('error1', 'Transfer initiated details could not be stored in the database');
                    }
                    else {
                        return redirect('/withdraw-requests');
                    }
              }

             else {
                if ($message == "otp") {

                    session()->put('transfer_code', $transfer_code);
                    session()->put('recipient_code', $recipient_code);
                    return view('/paystack_transfers.finalize_transfer');

                    exit();
                }
                else {
                    return redirect('/withdraw-requests')->with('error2', 'Transfer could not be initiated, Contact the developer - ' . $message);
                }
             }
    }

    public function finalizeTransfer(Request $request)
    {

        $transfer_code = session()->get('transfer_code');
        $recipient_code = session()->get('recipient_code');
        $otp = $request->input('otp');

        $url = $this->baseUrl . "/transfer/finalize_transfer";
        $fields = [
                "transfer_code" => $transfer_code,
                "otp" => $otp,
              ];
        $fields_string = http_build_query($fields);
 
          //open connection
          $ch = curl_init();
          
          //set the url, number of POST vars, POST data
          curl_setopt($ch,CURLOPT_URL, $url);
          curl_setopt($ch,CURLOPT_POST, true);
          curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authBearer);
          
          //So that curl_exec returns the contents of the cURL; rather than echoing it
          curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
          
          //execute post
          $result = curl_exec($ch);
          // echo $result;
          // var_dump($result);

          $finalize = json_decode($result);
          $status = $finalize->status;
          $message = $finalize->data->status;
          $reference = $finalize->data->reference;
          $amount = $finalize->data->amount;
          $reason = $finalize->data->reason;
          $transfer_code = $finalize->data->transfer_code;
          $createdAt = $finalize->data->createdAt;

          if ($status) {

            return redirect('/withdraw-requests')->with('transfer-success', 'Transfer Success!');

          }
          else
            return redirect('/withdraw-requests')->with('error3', 'Transfer could not be finalized.');
    }

    public function bulkTransfer(Request $request)
    {

        $selected_requests = request('data');

        $collection = new Collection();

        foreach($selected_requests as $item) {
            $individualItem = DB::table('withdraw_requests')
                                ->select('amount','recipient_code')
                                ->where('id', $item)
                                ->first();

                            $collection->push((object)[
                                'amount' => $individualItem->amount,
                                'recipient_code' => $individualItem->recipient_code,
                            ]);
        }

          $url = $this->baseUrl . "/transfer/bulk";

          $fields = [
            // "currency": "NGN",
            // "source": "balance",
            // "transfers" => $collection
          ];
          //   // echo $fields;

          // $fields_string = http_build_query($fields);



$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.paystack.co//transfer/bulk/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "currency": "NGN",
    "source": "balance",
    "transfers": [
        {
            "amount": 5,
            "recipient": "RCP_syjftpyh08uj1i0"
        },
        {
            "amount": 5,
            "recipient": "RCP_syjftpyh08uj1i0"
        }
    ]
}
',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer sk_test_0f483e7cb1cdec063fc003adc809354f0f6e39d1',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

// curl_close($curl);
// echo $response;


          // $finalize = json_decode($response); 
          // $status = $finalize->data->amount;


// echo $responseJSON;
          // dd($finalize);


        //   //open connection
        //   $ch = curl_init();
          
        //   //set the url, number of POST vars, POST data
        //   curl_setopt($ch,CURLOPT_URL, $url);
        //   curl_setopt($ch,CURLOPT_POST, true);
        //   curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        //   curl_setopt($ch, CURLOPT_HTTPHEADER,  $this->authBearer);
          
        //   //So that curl_exec returns the contents of the cURL; rather than echoing it
        //   curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
          
        //   //execute post
        //   $result = curl_exec($ch);
        //   // echo $result;
 
          $finalize = json_decode($response);
          $status = $finalize->status;
          // $message = $finalize->data->status;
          // $reference = $finalize->data->reference;
          // $amount = $finalize->data->amount;
          // $reason = $finalize->data->reason;
          // $transfer_code = $finalize->data->transfer_code;
          // $createdAt = $finalize->data->createdAt;

          if ($status) {

            echo "{\"status\" : \"success\",
            \"message\" : \"Transfer Success\"
            }";

          }
          else {
            echo "{\"status\" : \"error\",
            \"message\" : \"Transfer could not be finalized\"
        }";
          }

          // return $finalize;
        // echo $collection;
    }
}


