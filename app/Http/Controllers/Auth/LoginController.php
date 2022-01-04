<?php

namespace App\Http\Controllers\Auth;

use App\Balance;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Token;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function userVerify(Request $request){

        if(Auth::attempt([ 'email' => $request->username , 'password' => $request->password ], $request->remember)){ // login attempt
            //login successful, redirect the user to your preferred url/route...
             return 1;
        }
        else if(Auth::attempt([ 'phone' => $request->username , 'password' => $request->password ], $request->remember)){ 
             return 1;
        }
        else{
           return 0;
        }
    }
    
    // public function userVerify(Request $request){

    //     if(Auth::attempt([ 'email' => $request->username , 'password' => $request->password ], $request->remember)){ // login attempt
    //         //login successful, redirect the user to your preferred url/route...
    //          return 1;
    //     }
    //     else{
    //        return 0;
    //     }
    // }


    /*public function index(Request $request)
    {
        if(auth::check()){

            $user=auth()->user();
            $balance= Balance::where('user_id',$user->id)->get()->all();
            var_dump($balance);
            if($balance!=null)
            {
                foreach ($balance as $row)
                    $avail_balance=$row->balance;
                //var_dump($balance);
            }
            else{
                $avail_balance=0.0;
            }
            session([
                'avail_balance' => $avail_balance
            ]);
        }
        return view('tradabet-home-page');

    }*/

    /*public function login(Request $request)
    {
        $this->validateLogin($request);

        //retrieveByCredentials
        if ($user = app('auth')->getProvider()->retrieveByCredentials($request->only('email', 'password'))) {
            $token = Token::create([
                'user_id' => $user->id
            ]);

            if ($token->sendCode()) {
                session()->set("token_id", $token->id);
                session()->set("user_id", $user->id);
                session()->set("remember", $request->get('remember'));

                return redirect("code");
            }

            $token->delete();// delete token because it can't be sent
            return redirect('/login')->withErrors([
                "Unable to send verification code"
            ]);
        }

        return redirect()->back()
            ->withInputs()
            ->withErrors([
                $this->username() => Lang::get('auth.failed')
            ]);
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            if($finduser){

                Auth::login($finduser);

               return redirect('/home');

            }else{
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id
                ]);

                Auth::login($newUser);

                return redirect()->back();
            }

        } catch (Exception $e) {
            return redirect('auth/google');
        }
    }*/
}
