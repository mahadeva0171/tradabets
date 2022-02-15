<?php

namespace App\Helpers;
use App\Balance;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class PaymentHelper
{
    public static function update_transaction($amount, $user_id, $reference, $status)
    {
        $avail_balance = Balance::where('user_id', $user_id)->first()->balance;
        $balance_amt = $avail_balance + $amount;
        $final_balance = DB::table('balance')
            ->where('user_id', $user_id)
            ->update(['balance' => $balance_amt]);

        session([
            'avail_balance' => $balance_amt
        ]);
        $update_details = ['status' => $status,'amount' => $amount,'opening_balance' => $avail_balance,'closing_balance' => $balance_amt];
        $status_update = DB::table('transaction')
            ->where('id', $reference)
            ->update($update_details);

        // \App\Models\Transaction::create(['user_id' => $user_id,
        //     'status' => $status,
        //     'amount' => $amount,
        //     'opening_balance' => $avail_balance,
        //     'closing_balance' => $balance_amt,]);
    }

    public static function initiate_transaction($user_id, $status, $amount)
    {

       $data = \App\Models\Transaction::create(['user_id' => $user_id,
            'status' => $status,
            'amount' => ($amount/100)]);

       $data->save();
       return $data->id;
    }

    public static function get_user_id_by_reference($reference)
    {
        return Transaction::where('id', $reference)->first()->user_id;
    }

    public static function create_transaction($amount, $user_id,$status)
    {
        $avail_balance = Balance::where('user_id', $user_id)->first()->balance;
        $balance_amt = $avail_balance + $amount;
        $final_balance = DB::table('balance')
            ->where('user_id', $user_id)
            ->update(['balance' => $balance_amt]);
        session([
            'avail_balance' => $balance_amt
        ]);
        \App\Models\Transaction::create(['user_id' => $user_id,
            'status' => $status,
            'amount' => $amount,
            'opening_balance' => $avail_balance,
            'closing_balance' => $balance_amt,]);

        //bonus amount can be set here for Play credit
    }
}
