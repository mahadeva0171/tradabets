<?php
namespace Unicodeveloper\Paystack;
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\UserBankDetails;
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

        $recipient_code = DB::table('user_bank_accounts')
            ->select('recipient_code')
            ->where(['user_id', $user->user_id],['Active_status',"Active"]);

        $amount = round($user->amount * 100);
        // $stored_recipient_code = '';

        $reason = "Withdrawal transfers";

        // $check_recipient = UserBankDetails::select("*")
        //                   ->where('recipient_code', $recipient_code)
        //                   ->exists();

        //  if ($check_recipient) {
        //     $data = mysqli_fetch_array($check_recipient);
        //     $stored_recipient_code = $data['recipient_code'];
        //  }
         // if ($recipient_code !== $stored_recipient_code) {
         //    echo "<script>alert('Recipient Code stored does not match with the code received');</script>";
         // }
         // else {

              $url = $this->baseUrl . "/transfer";
              $fields = [
                'source' => "balance",
                'amount' => $amount,
                'recipient' => $recipient_code,
                'reason' => "$reason"
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

              $initiate = json_decode($result);
              // echo $result;
               $status = $initiate->status;
               $message = $initiate->message;

              if ($status == true && $message == "success") {

                  $message = $initiate->data->status;
                  $reference = $initiate->data->reference;
                  $amount = $initiate->data->amount;
                  $reason = $initiate->data->reason;
                  $transfer_code = $initiate->data->transfer_code;
                  $createdAt = $initiate->data->createdAt;

                  $data_objects = ['transfer_code'=>$transfer_code, 'recipient_code'=>$recipient_code];
                    $values = array('reference' => '$reference', 'amount_in_cobo' => '$amount', 'reason' => '$reason', 'status' => '$message', 'transfer_code' => '$transfer_code', 'createdAt' => '$createdAt');
                    $query =  DB::table('paystack_transfer_initiate')->insert($values);

                    $update = DB::table('withdraw_requests')
                        ->where('id', $id)
                        ->update(['status' => "approved"]);

                    if (!$query) {
                        // code...
                        echo "<script> alert('Error: Transfer initiated details could not be stored in the database'); </script>";
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
                    echo "<script> alert('Error: Transfer could not be initiated, Contact the developer - " . $message ."' ); </script>";

                }
             }
        // }
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

            // $values = array('reference' => '$reference', 'amount_in_cobo' => '$amount', 'reason' => '$reason', 'status' => '$message', 'transfer_code' => '$transfer_code', 'createdAt' => '$createdAt');
            // $query =  DB::table('paystack_transfer_initiate')->insert($values);

            // if (!query) {
            //     // code...
            //     echo "<script> alert('Error: Transfer initiated details could not be stored in the database'); </script>";
            // }
            // else {
            //     return redirect('/withdraw-requests');
            // }
            echo "<script> alert('Error: Transfer success!'); </script>";

          }
          else
            echo "<script> alert('Error: Transfer could not be finalized'); </script>";
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
                                'reason' => "Transfer for Withdrawal request",
                            ]);
        }

          $url = $this->baseUrl . "/transfer/bulk";

          $fields = [
            'currency' => "NGN",
            'source' => "balance",
            'transfers' => $collection,
          ];

          $fields_string = http_build_query($fields);
          //open connection
          $ch = curl_init();
          
          //set the url, number of POST vars, POST data
          curl_setopt($ch,CURLOPT_URL, $url);
          curl_setopt($ch,CURLOPT_POST, true);
          curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
          curl_setopt($ch, CURLOPT_HTTPHEADER,  $this->authBearer);
          
          //So that curl_exec returns the contents of the cURL; rather than echoing it
          curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
          
          //execute post
          $result = curl_exec($ch);
          // echo $result;

          $finalize = json_decode($result);
          $status = $finalize->status;
          // $message = $finalize->data->status;
          // $reference = $finalize->data->reference;
          // $amount = $finalize->data->amount;
          // $reason = $finalize->data->reason;
          // $transfer_code = $finalize->data->transfer_code;
          // $createdAt = $finalize->data->createdAt;

          if ($status) {

            // $values = array('reference' => '$reference', 'amount_in_cobo' => '$amount', 'reason' => '$reason', 'status' => '$message', 'transfer_code' => '$transfer_code', 'createdAt' => '$createdAt');
            // $query =  DB::table('paystack_transfer_initiate')->insert($values);

            // if (!query) {
            //     // code...
            //     echo "<script> alert('Error: Transfer initiated details could not be stored in the database'); </script>";
            // }
            // else {
            //     return redirect('/withdraw-requests');
            // }
            echo "<script> alert('Error: Transfer success!'); </script>";

          }
          else
            echo "<script> alert('Error: Transfer could not be finalized'); </script>";
          echo $result;
    }


}
