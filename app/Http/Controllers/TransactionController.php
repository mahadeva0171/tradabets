<?php

namespace App\Http\Controllers;

use App\Balance;
use App\UserBankDetails;
use App\Models\InboxNotification;
use App\Models\Transaction;
use App\KycDocument;
use App\User;
use App\WithdrawRequest;
use App\PaymentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user=auth()->user();

        $view_data=[];

        $filter_arr = [
            'date_from' => date("Y-m-d", strtotime("last week saturday")),
            'date_to' => date("Y-m-d", strtotime("tomorrow")),
        ];
        if($request->form){
            $transaction=Transaction::where('user_id',$user->id)
                ->whereBetween('created_at',array($request->form['date_from'],$request->form['date_to']))->get()->all();
        }
        else
        {
            $transaction=Transaction::where('user_id',$user->id)
                ->whereBetween('created_at',array($filter_arr['date_from'],$filter_arr['date_to']))->get()->all();
        }
        $filter_arr = ($request->form) ? array_merge($filter_arr, $request->form) : $filter_arr;

        $balance= Balance::where('user_id',$user->id)->get()->all();

        $view_data=['transaction'=>$transaction,'user'=>$user,'filter_arr'=>$filter_arr,'balance'=>$balance];

        return view('transaction-list.transaction-list-index',$view_data);
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deposit(Request $request)
    {
        $user=auth()->user();

        $view_data=[];
        $date=now();

        $filter_arr = [
            'date_from' => date("Y-m-d", strtotime("last week saturday")),
            'date_to' => date("Y-m-d", strtotime("tomorrow")),
        ];
        if($request->form){
            $transaction=Transaction::where([['user_id',$user->id],['status','deposit']])
                ->whereBetween('created_at',array($request->form['date_from'],$request->form['date_to']))->get()->all();
        }
        else
        {
            $transaction=Transaction::where([['user_id',$user->id],['status','deposit']])
                ->whereBetween('created_at',array($filter_arr['date_from'],$filter_arr['date_to']))->get()->all();
        }
        $filter_arr = ($request->form) ? array_merge($filter_arr, $request->form) : $filter_arr;

        $balance= Balance::where('user_id',$user->id)->get()->all();

        $view_data=['transaction'=>$transaction,'user'=>$user,'filter_arr'=>$filter_arr,'balance'=>$balance];

        return view('transaction-list.deposit-index',$view_data);
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function withdraw(Request $request)
    {
        $user=auth()->user();

        $view_data=[];

        $filter_arr = [
            'date_from' => date("Y-m-d", strtotime("last week saturday")),
            'date_to' => date("Y-m-d", strtotime("tomorrow")),
        ];
        if($request->form){
            $withdraw=WithdrawRequest::where('user_id',$user->id)
                ->whereBetween('created_at',array($request->form['date_from'],$request->form['date_to']))->get()->all();
        }
        else
        {
            $withdraw=WithdrawRequest::where('user_id',$user->id)
                ->whereBetween('created_at',array($filter_arr['date_from'],$filter_arr['date_to']))->get()->all();
        }
        $filter_arr = ($request->form) ? array_merge($filter_arr, $request->form) : $filter_arr;


        $view_data=['withdraw'=>$withdraw,'user'=>$user,'filter_arr'=>$filter_arr];

        return view('transaction-list.withdraw-index',$view_data);
    }

    public function depositForm(Request $request)
    {
        $user=auth()->user();

        $request_amount=($request->amount!=null)? $request->amount : 0.0;

        $avail_balance= Balance::where('user_id',$user->id)->first()->balance;

        $view_data=['avail_balance'=>$avail_balance,'request_amount'=>$request_amount];

        return view('transaction-list.deposit-form',$view_data);
    }

    public function withdrawForm(Request $request)
    {
        $user=auth()->user();

        $total_balance = Balance::where('user_id',$user->id)->first()->balance;
        $check_bonus = Transaction::where(['user_id'=>$user->id, 'status'=>'bonus'])->first();
        $bonus = (($check_bonus)) ? $check_bonus->amount : 0 ;

        $avail_balance = ($total_balance - $bonus);
        $view_data=['avail_balance'=>$avail_balance];

        return view('transaction-list.withdraw-request',$view_data);
    }

    public function reverseWithdraw(Request $request, WithdrawRequest $withdraw)
    {
        $user=Auth()->user();
        $reverse_withdraw_amount=WithdrawRequest::where([['user_id',$user->id],['id',$withdraw->id]])->first()->amount;
        if($reverse_withdraw_amount!=null) {
            $current_balance = Balance::where('user_id',$user->id)->first()->balance;
            $available_balance = $current_balance + $reverse_withdraw_amount;
            $final_balance = DB::table('balance')
            ->where('user_id', $user->id)
            ->update(['balance' => $available_balance]);
             session([
            'avail_balance' => $available_balance
        ]);
             $form=[];
             $form['status'] = 'reversed';
             $withdraw->update($form);
             Transaction::create(['user_id'=>$user->id,
            'status'=>'reversed',
            'amount'=>$reverse_withdraw_amount,
            'opening_balance'=>$current_balance,
            'closing_balance'=>$available_balance]);

            session()->flash('message-success', 'Amount successfully returned to your wallet.');

            return redirect('/withdraw');
        }
        else{
            return view('_security.restricted-area.show');
        }
    }

    public function withdrawAmount(Request $request)
    {
        
        $user=auth()->user();

        // $kyc_update=KycDocument::where([['user_id',$user->id],['status','approved']])->get()->all();
        // if($kyc_update!=null)
        // {
        //   $kyc_status = 1;
        // }
        // else
        // {
        //     $kyc_update=KycDocument::where([['user_id',$user->id],['status','pending']])->get()->all();             
        //     if($kyc_update!=null)
        //         {
        //             $kyc_status = 2;
        //         }
        //         else
        //         {
        //             $kyc_status = 0;

        //         }

        // }
        // if ($kyc_status != 1) {
        //     return redirect('/withdraw');
        // }

        $avail_balance= Balance::where('user_id',$user->id)->first()->balance;
        $balance_amt=$avail_balance-$request->withdraw_amount;
        $recipient_code = DB::table('user_bank_accounts')
                            ->where(['user_id' => $user->id])
                            ->where(['Active_status' => "Active"])
                            ->pluck('recipient_code');

        WithdrawRequest::create(['user_id'=>$user->id,
                                 'status'=>'pending',
                                 'amount'=>$request->withdraw_amount,
                                 'recipient_code'=> $recipient_code[0],
                               ]);

        Transaction::create(['user_id'=>$user->id,
            'status'=>'withdraw',
            'amount'=>$request->withdraw_amount,
            'opening_balance'=>$avail_balance,
            'closing_balance'=>$balance_amt]);

        $final_balance = DB::table('balance')
            ->where('user_id', $user->id)
            ->update(['balance' => $balance_amt]);
        session([
            'avail_balance' => $balance_amt
        ]);
        $super_admin = user::where('role', 'admin')->first()->id;
        InboxNotification::create([
            'receiver'=>$super_admin,
            'subject'=>'Regarding Withdraw Request',
            'body'=>'User '.$user->first_name .' '.$user->last_name.' requested to withdraw the amount.'
        ]);
        //$balance->update($form_bal);

       return redirect('/withdraw');
    }
    
    public function adminTransactionView(Request $request)
    {
        $view_data=[];
        $user=Auth()->user();
        if($user->role=='admin') {
            $filter_arr = [
                'date_from' => date("Y-m-d", strtotime("last week saturday")),
                'date_to' => date("Y-m-d", strtotime("tomorrow")),
                'user' => null,
                'status' => null,
            ];
            if ($request->form) {
                if ($request->form['user'] != null && $request->form['status'] != null) {
                    $users = user::where('first_name', 'like', $request->form['user'] . '%')->get()->pluck('id')->toArray();
                    $transaction = Transaction::where('status', $request->form['status'])->whereIn('user_id', $users)
                        ->whereBetween('created_at', array($request->form['date_from'], $request->form['date_to']))->get()->all();
                } else if ($request->form['user'] != null) {
                    $users = user::where('first_name', 'like', $request->form['user'] . '%')->get()->pluck('id')->toArray();
                    $transaction = Transaction::whereIn('user_id', $users)
                        ->whereBetween('created_at', array($request->form['date_from'], $request->form['date_to']))->get()->all();
                } else if ($request->form['status'] != null) {
                    $transaction = Transaction::where('status', $request->form['status'])
                        ->whereBetween('created_at', array($request->form['date_from'], $request->form['date_to']))->get()->all();
                } else {

                    $transaction = Transaction::whereBetween('created_at', array($request->form['date_from'], $request->form['date_to']))->get()->all();
                }
            } else {
                $transaction = Transaction::whereBetween('created_at', array($filter_arr['date_from'], $filter_arr['date_to']))->get()->all();
            }
            $filter_arr = ($request->form) ? array_merge($filter_arr, $request->form) : $filter_arr;
            $users = user::select_list()->all();

            // $balance= Balance::where('user_id',$user->id)->get()->all();

            $view_data = ['transaction' => $transaction, 'users' => $users, 'filter_arr' => $filter_arr];

            return view('admin-views.transaction.admin-transaction-list', $view_data);
        }
        else{
            return view('_security.restricted-area.show');
        }
    }

    public function adminBalanceView(Request $request)
    {
        $user=Auth()->user();
        if($user->role=='admin') {
            $filter_arr = [
                'user' => null,
            ];
            if ($request->form) {
                if ($request->form['user'] != null) {
                    $users = user::where('first_name', 'like', $request->form['user'] . '%')->get()->pluck('id')->toArray();
                    $balance = Balance::WhereIn('user_id', $users)
                        ->get()->all();
                } else {

                    $balance = Balance::all();
                }
            } else {
                $balance = Balance::all();
            }
            $filter_arr = ($request->form) ? array_merge($filter_arr, $request->form) : $filter_arr;
            $users = user::select_list()->all();

            // $balance= Balance::where('user_id',$user->id)->get()->all();

            $view_data = ['balance' => $balance, 'users' => $users, 'filter_arr' => $filter_arr];

            return view('admin-views.transaction.admin-balance-list', $view_data);
        }
        else
        {
            return view('_security.restricted-area.show');
        }
    }

    public function withdrawRequestLists(Request $request)
    {
        $user=Auth()->user();
        if($user->role=='admin') {
            $withdraw_requests = WithdrawRequest::where('status', 'pending')->get()->all();
            $view_data = ['withdraw_requests' => $withdraw_requests];
            return view('admin-views.transaction.admin-withdraw-requests-list', $view_data);
        }
        else{
            return view('_security.restricted-area.show');
        }
    }

    public function withdrawRequestListsView(Request $request, WithdrawRequest $withdraw)
    {
        $user=Auth()->user();
        if($user->role=='admin') {
            $view_data=['withdraw'=>$withdraw];
            return view('admin-views.transaction.admin-withdraw-requests-view',$view_data);
        }
        else{
            return view('_security.restricted-area.show');
        }
    }

    public function withdrawRequestListsUpdate(Request $request, WithdrawRequest $withdraw)
    {
        $form=[];
        $user=Auth()->user();
        if($user->role=='admin') {
            $form = $request->form;
            $withdraw->update($form);

            if($form['status']=='rejected')
            {
               $avail_balance= Balance::where('user_id',$withdraw->user_id)->first()->balance;
                if($avail_balance!=null)
                {
                    $balance_amt=$avail_balance+$withdraw->amount;
                    $final_balance = DB::table('balance')
                        ->where('user_id',$withdraw->user_id)
                        ->update(['balance' => $balance_amt]);
                    session([
                        'avail_balance' => $balance_amt
                    ]);
                    \App\Models\Transaction::create(['user_id'=>$withdraw->user_id,
                    'status'=>'rejected',
                    'amount'=>$withdraw->amount,
                    'opening_balance'=>$avail_balance,
                    'closing_balance'=>$balance_amt,]);
                    //var_dump($balance);
                }
            }
            InboxNotification::create([
                'receiver' => $withdraw->user_id,
                'subject' => 'Regarding withdraw status',
                'body' => $withdraw->remarks,
            ]);
            return redirect('/withdraw-requests');
        }
        else{
            return view('_security.restricted-area.show');
        }
    }

    public function withdrawRequestIndividualUpdate(Request $request, $id)
    {
        $user = DB::table('withdraw_requests')
            ->select('user_id')
            ->where('id', $id)
            ->first();
        
        $update = DB::table('withdraw_requests')
            ->where('id', $id)
            ->update(['status' => "approved"]);

        $recipient_code = DB::table('user_bank_accounts')
            ->select('recipient_code')
            ->where(['id', $id],['user_id', $user]);

        return redirect('/withdraw-requests');
    }

    public function withdrawRequestIndividualRejectUpdate(Request $request, $id)
    {
        $update = DB::table('withdraw_requests')
            ->where('id', $id)
            ->update(['status' => "rejected"]);

        $result = DB::table('withdraw_requests')
            ->select('user_id','amount')
            ->where('id', $id)
            ->first();

             $avail_balance= Balance::where('user_id',$result->user_id)->first()->balance;
                if($avail_balance!=null)
                {
                    $balance_amt=$avail_balance+$result->amount;
                    $final_balance = DB::table('balance')
                        ->where('user_id',$result->user_id)
                        ->update(['balance' => $balance_amt]);
                    session([
                        'avail_balance' => $balance_amt
                    ]);
                   $transaction = Transaction::create(['user_id'=>$result->user_id,
                    'status'=>'rejected',
                    'amount'=>$result->amount,
                    'opening_balance'=>$avail_balance,
                    'closing_balance'=>$balance_amt,]);
                }

        return redirect('/withdraw-requests');
    }

    public function withdrawRequestBulkRejectUpdate(Request $request)
    {

        $selected_requests = request('data');

        // $test = DB::table('withdraw_requests')
        //         ->select('amount')
        //         ->where('id', selected_requests[0]);

        return $selected_requests[0];

    }

    public function paystackPaymentReport(Request $request)
    {
        $view_data=[];
        $user=Auth()->user();
        if($user->role=='admin') {
            $filter_arr = [
                'date_from' => date("Y-m-d", strtotime("last week saturday")),
                'date_to' => date("Y-m-d", strtotime("today")),
            ];

            $payment = PaymentReport::all();

            $view_data = ['payment' => $payment, 'filter_arr' => $filter_arr];

            return view('admin-views.transaction.payment-transaction-report', $view_data);
        }
        else{
            return view('_security.restricted-area.show');
        }
    }

}
