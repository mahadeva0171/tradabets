<?php

use App\Balance;
use App\KycDocument;
use App\UserBankDetails;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	if(auth::check()){
		$user=auth()->user();
		if($user->role!='admin'){
		$balance= Balance::where('user_id',$user->id)->first()->balance;
		$kyc_update=KycDocument::where([['user_id',$user->id],['status','approved']])->get()->all();
		$bank_account=userBankDetails::where([['user_id',$user->id],['Active_status','Active']])->get()->all();
	
		if($bank_account!=null)
		{
			session([
				'account_status' => 1
			]);
		}
		else
		{
			session([
				'account_status' => 0
			]); 
			
		}

		if($kyc_update!=null)
		{
			session([
			'kyc_status' => 1
		]);
		}
		else
		{
			$kyc_update=KycDocument::where([['user_id',$user->id],['status','pending']])->get()->all();             
			if($kyc_update!=null)
				{
					session([
					'kyc_status' => 2
					]);
				}
				else
				{
				   session([
					'kyc_status' => 0
					]); 
				}

		}
		$avail_balance=($balance!=null)? $balance : 0.0;
		session([
			'avail_balance' => $avail_balance
		]);

			return view('tradabet-home-page');
	}
		else{
			   return redirect('/home');
		}

	}
   return view('tradabet-home-page');

})->name('/');
Route::get('google', function () {
	return view('googleAuth');
});
Route::get('sports',function(){
	return view('menu-pages.sports');
})->name('sports');
Route::get('casino',function(){
	return view('menu-pages.casino');
})->name('casino');
Route::get('games',function(){
	return view('menu-pages.games');
})->name('games');
Route::get('poker',function(){
	return view('menu-pages.poker');
})->name('poker');
Route::get('promotions',function(){
	return view('menu-pages.promotions');
})->name('promotions');

Route::get('auth/google', 'Auth\LoginController@redirectToGoogle');
Route::get('auth/google/callback', 'Auth\LoginController@handleGoogleCallback');
Route::get('/complete-registration', 'Auth\RegisterController@completeRegistration');
Route::post('register', 'Auth\RegisterController@register');
Route::get('register',function(){
	return redirect ('/');
})->name('register');
Route::get('login',function(){
	return redirect ('/');
})->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('login/userVerify','Auth\LoginController@userVerify');
Route::get('/emailCheck/{postdata}', 'Auth\RegisterController@emailCheck');
Route::get('/phoneCheck/{postdata}', 'Auth\RegisterController@phoneCheck');

// password reset
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::get('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// verify e-mail
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

//Route::post('register', 'Auth\RegisterController@showRegistrationForm');


Auth::routes(['register'=>false,'login'=>false]);

$middleware=['auth','verified'];

Route::middleware($middleware)->get('/home', 'HomeController@index')->name('home');
	//Bet-list
Route::middleware($middleware)->get('/betlist', 'BetListController@index');
Route::middleware($middleware)->get('/betlist-cashout', 'BetListController@betListCashout');
	//Bonus
Route::middleware($middleware)->get('/active-bonus', 'BonusController@index');
Route::middleware($middleware)->get('/bonus-transaction-list', 'BonusController@bonusTransactionList');
	//Rewards
Route::middleware($middleware)->get('/rewards', 'RewardsController@index');
	//Transaction List
Route::middleware($middleware)->get('/transaction', 'TransactionController@index');
Route::middleware($middleware)->get('/deposits', 'TransactionController@deposit');
Route::middleware($middleware)->get('/deposit-form', 'TransactionController@depositForm');
Route::middleware($middleware)->get('/deposit-form/{amount}', 'TransactionController@depositForm');
Route::middleware($middleware)->get('/payment-request', 'PaymentController@payment');
Route::middleware($middleware)->get('/deposit-request', 'PaymentController@depositAmount')->name('deposit-request');
Route::middleware($middleware)->get('/withdraw', 'TransactionController@withdraw');
Route::middleware($middleware)->get('/withdraw-request-form', 'TransactionController@withdrawForm');
Route::middleware($middleware)->get('/withdraw-request', 'TransactionController@withdrawAmount');
Route::middleware($middleware)->get('/reverse-withdraw/{withdraw}', 'TransactionController@reverseWithdraw');
Route::middleware($middleware)->get('/transaction-view', 'TransactionController@adminTransactionView');
Route::middleware($middleware)->get('/balance-view', 'TransactionController@adminBalanceView');
Route::middleware($middleware)->get('/withdraw-requests', 'TransactionController@withdrawRequestLists');
Route::middleware($middleware)->get('/withdraw-request/view/{withdraw}', 'TransactionController@withdrawRequestListsView');
Route::middleware($middleware)->get('/withdraw-request/update/{withdraw}', 'TransactionController@withdrawRequestListsUpdate');
Route::middleware($middleware)->get('/withdraw-request-individual/update/{id}', 'TransactionController@withdrawRequestIndividualUpdate');
Route::middleware($middleware)->get('/withdraw-request-individual-reject/update/{id}', 'TransactionController@withdrawRequestIndividualRejectUpdate');
Route::middleware($middleware)->post('/withdraw-request-bulk-reject', 'TransactionController@withdrawRequestBulkRejectUpdate');
Route::middleware($middleware)->get('/transaction-report', 'TransactionController@paystackPaymentReport');


	// user profile
Route::middleware($middleware)->get('users/profile/{user}', 'UserProfileController@show');
Route::middleware($middleware)->get('users/profile/{user}/edit', 'UserProfileController@edit');
Route::middleware($middleware)->patch('users/profile/{user}', 'UserProfileController@update');
Route::middleware($middleware)->get('/developers','DevelopersController@index');

	//KYC
Route::middleware($middleware)->get('/document-upload', 'KycController@index');
Route::middleware($middleware)->get('/kyc-upload-form', 'KycController@documentShow');
Route::middleware($middleware)->post('/kyc-upload', 'KycController@upload');
Route::middleware($middleware)->get('/kyc-list', 'KycController@docList');
Route::middleware($middleware)->get('/kyc-list/view/{document}', 'KycController@viewDoc');
Route::middleware($middleware)->post('/kyc-list/update/{document}', 'KycController@update');
Route::middleware($middleware)->get('/document-show/{id}', 'KycController@show');

	//Inbox
/*Route::middleware($middleware)->get('/inbox/mark-all-as-read', 'InboxNotificationController@mark_all_as_read');*/
Route::middleware($middleware)->get('/inbox/message-view/{notification}', 'InboxNotificationController@mark_all_as_read');
Route::middleware($middleware)->resource('inbox', 'InboxNotificationController')->parameters([
		'inbox' => 'inbox_notification'
	]);

	// Paystack
Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

	//BankAccounts
Route::middleware($middleware)->get('/bank-accounts', 'BankAccountsController@index')->name('bank_account');
Route::middleware($middleware)->get('/add-bank-account', 'BankAccountsController@addAccount');
Route::middleware($middleware)->post('/add_account', 'BankAccountsController@add');
Route::middleware($middleware)->get('/activate-account/{id}', 'BankAccountsController@activateAccount');

	//Paystack transfers
Route::middleware($middleware)->get('/activate-account/{}', 'BankAccountsController@activateAccount');
Route::middleware($middleware)->get('/initiate_transaction/{id}', 'PaystackController@initiate');
Route::middleware($middleware)->get('/finalize_transfer', 'PaystackController@finalizeTransfer')->name('otp');

Route::middleware($middleware)->post('/bulkTransfer', 'PaystackController@bulkTransfer');





