@php
    $avail_balance = Session::get('avail_balance');
@endphp

    <section class="body">

         <div class="col-lg-12 home-page-header">

             <div class="container container-template">

                    <ul class="notifications">
        <li>
            <a href="#" class="">
                FAQ's
            </a></li>
            <li>
            <a href="#" class="">
                Responsible Gambling
            </a></li>
            <li class="contact-us-list-item">
            <a href="#" class="">
                Contact Us
            </a></li></ul>
             </div>

            </div>

<!-- </div> -->
            <div class="col-lg-12 row game-list-header">
                <div class="container menu-container container-template">
                <div class="col-lg-2 logo-container">
                <div class="col-lg-4 logo-div">
                <a href="{{route('/')}}" class="logo">
                    <img src="/themes/admin/img/logo-placeholder.png" alt="Tradabet" />
                </a></div><div></div><div class="responsive-menu"><i class="fa fa-bars"></i></div></div>
                <div class=" col-lg-10 games-list-menu">

                 <ul class="gaming-menu">
      <li class="sports-list {{ (request()->segment(1) == 'sports') ? 'active' : '' }}"><a href="{{route('sports')}}">Sports</a></li>
      <li class="sports-list {{ (request()->segment(1) == 'casino') ? 'active' : '' }}"><a href="{{route('casino')}}">Casino</a></li>
      <li class="sports-list {{ (request()->segment(1) == 'games') ? 'active' : '' }}"><a href="{{route('games')}}">Games</a></li>
      <li class="sports-list {{ (request()->segment(1) == 'poker') ? 'active' : '' }}"><a href="{{route('poker')}}">Poker</a></li>
      <li class="sports-list {{ (request()->segment(1) == 'promotions') ? 'active' : '' }}"><a href="{{route('promotions')}}">Promotions</a></li>
                     @if(auth::check())
                         <li class="sports-list myaccount-menu-item"><a href="#" data-toggle="dropdown" id="userbox">My Account <i class="fa custom-caret"></i></a>
                             <ul class="dropdown-menu list-unstyled mb-2 my-account-dropdown">
                                 <li>
                                     <input type="hidden"class="balance-amount-class" value="{{$avail_balance}}">
                                     <span class="dropdown-balance">Available Balance<br><span class="amount-balance">&#8358;&nbsp;</span><label class="user-balance-label"></label></span></li>
                                 <li class="divider dropdown-divider"></li>
                                 <li class="profile-list">
                                     <a class="dropdowm-item dashboard-anchor" tabindex="-1" href="{{ route('home') }}" target="_blank"><i class="fas fa-user" style="padding-right:10px"></i> My Profile</a>
                                 </li>
                                 <li>
                                     <a class="dropdown-item dashboard-logout" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                         <i class="fa fa-power-off" style="padding-right:13px"></i>{{ __('Logout') }}
                                     </a>

                                     <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                         @csrf
                                     </form>
                                 </li>
                             </ul>
                         </li>
                     @else
    <li class="button-list"><button id="game-login-btn-id" class="btn game-login-button-class"data-toggle="modal" data-target="#loginModal">Login</button></li>
    <li class="button-list"><button id="game-join-btn-id" class="btn btn-primary game-login-button-class"data-toggle="modal" data-target="#registerModal">Join</button></li>
                         @endif
    </ul>
</div>
            </div>

            </div>


        <!--login Modal-->
        <div id="loginModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><div class="card-title-sign mt-3 text-right">
                                <h2 class="title text-uppercase font-weight-bold m-0"><i class="fas fa-user mr-1"></i> Sign In</h2>
                            </div></h4>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">

                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            {!! Form::open(['url' => '/login', 'class' => 'user-login-form']) !!}


                            <div class="form-group mb-3">
                                <label>E-Mail Address/ Phone Number</label>
                                <div class="input-group">
                                    {{ Form::text('email', old('user_name'), ['class' => 'form-control form-control-lg login-email', 'required' => true, 'autofocus' => true, 'tabindex' => 1]) }}
                                    <span class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </span>
                                </div>
                                <span id="email-message"></span>
                            </div>

                            <!-- <div class="form-group mb-3">
                                <label>E-Mail Address</label>
                                <div class="input-group">
                                    {{ Form::text('email', old('user_name'), ['class' => 'form-control form-control-lg login-email', 'required' => true, 'autofocus' => true, 'onfocusout'=>'loginEmailVerify()', 'tabindex' => 1]) }}
                                    <span class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </span>
                                </div>
                                <span id="email-message"></span>
                            </div> -->

                            <div class="form-group mb-3">
                                <div class="clearfix">
                                    <label class="float-left">Password</label>
                                    <a href="#" class="float-right" data-toggle="modal" onclick="forgotPasswordModalShow()">Lost Password?</a>
                                </div>
                                <div class="input-group">
                                    {{ Form::password('password', ['class' => 'form-control form-control-lg login-password', 'required' => true,'onfocusout'=>'loginPasswordVerify()', 'onkeypress'=>'loginPasswordMessageClear()', 'tabindex' => 2]) }}
                                    <span class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye-slash is-show-password-icon hover-icon-cursor-pointer"></i>
                                    </span>
                                </span>
                                </div>
                                <span id="password"></span>
                            </div>

                            <div class="row">

                                <div class="col-sm-4 ">
                                    <button type="button" class="btn btn-primary mt-2" onclick="userVerify()">Login</button>
                                </div>
                                {{--<a href="{{ url('auth/google') }}" class="btn btn-lg btn-primary btn-block">
                                    <strong>Login With Google</strong>
                                </a>--}}
                            </div>

                           {{-- <span class="mt-3 mb-3 line-thru text-center text-uppercase">
                            <span>or</span>
                        </span>--}}
{{--
                            <p class="text-center">Don't have an account yet? <a href="#" data-toggle="modal" data-target="#registerModal" >Sign Up!</a></p>--}}

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Register Modal -->
        <div id="registerModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><div class="card-title-sign mt-3 text-right">
                                <h2 class="title text-uppercase font-weight-bold m-0"><i class="fas fa-user mr-1"></i>Register</h2>
                            </div></h4>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">

                        {!! Form::open(['url' => '/register', 'class' => '','name'=>'registration']) !!}

                        <fieldset id="first-page">
                            <div class="form-group mb-3">
                                <label>First Name</label>
                                {{ Form::text('first_name', old('first_name'), ['class' => 'form-control first-name', 'required' => true,'onkeypress'=>'return onlyCharacters(event,this)']) }}
                            </div>

                            <div class="form-group mb-3">
                                <label>Last Name</label>
                                {{ Form::text('last_name', old('last_name'), ['class' => 'form-control last-name', 'required' => true, 'onkeypress'=>'return onlyCharacters(event,this)']) }}
                            </div>

                            <div class="form-group mb-3">
                                <label>Date of Birth</label>
                                {{--<input class="form-control" name="form[date_of_birth]" type="text" id="datepicker"><span><i class="fas fa-calendar-alt"></i></span>--}}
                                <div class="input-group"><input class="form-control date-of-birth" name="date_of_birth" type="text" value="" id="datepicker" required autocomplete="false"><span class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></span></div>
                            </div>

                            <div class="form-group mb-3">
                                <label>E-Mail Address</label>
                                {{ Form::text('email', old('email'), ['class' => 'form-control email-input', 'onfocusout' =>'emailVerify()', 'onkeypress'=>'emailMessageClear()']) }}
                                <span id="email-id"></span>
                            </div>

                            <div class="form-group mb-0">
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label>Password</label>
                                        {{ Form::password('password', ['class' => 'form-control password', 'required' => true, 'id'=>'password','onfocusout' =>'passwordVerify()', 'onkeypress'=>'passwordMessageClear()']) }}
                                        <span id="password-message"></span>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label>Password Confirmation</label>
                                        {{ Form::password('password_confirmation', ['class' => 'form-control confirm-password', 'required' => true,'onfocusout' =>'confirmPasswordVerify()', 'onkeypress'=>'confirmPasswordMessageClear()']) }}
                                        <span id="pwd-message"></span>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-8">
                                    <span class="mandatory-fields"></span>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <button type="button"  class="next action-button btn btn-primary mt-2" onclick="registerToggle()">Next</button>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset id="second-page">

                           {{-- <div class="form-group mb-0">
                                <div class="row">
                                    <div class="col-sm-10 mb-3">
                                        <label>Country</label>
                                        {{ Form::text('country','Nigeria', ['class' => 'form-control readonly', 'required' => true]) }}<span></span>
                                    </div>
                                  --}}
                                  {{--  <div class="col-sm-2 mb-3">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" id="province_fetchID" title="Click here to select the province."><i class="fa fa-map-pin"></i></button>
                                    </div>--}}
                                    {{--
                                </div>
                            </div>--}}

                            <div class="form-group mb-3">
                                <label>Phone</label>
                                <!-- {{ Form::text('phone', old('phone'), ['class' => 'form-control phone', 'required' => true,'maxlength'=>12, 'onkeypress'=>'return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57','onfocusout' =>'phoneNumberVerify()', 'onkeydown'=>'phoneMessageClear()']) }} -->
                                    
                                <!-- {{ Form::text('phone', old('phone'), ['class' => 'form-control phone', 'required' => true,'maxlength'=>12, 'onfocusout' =>'phoneNumberVerify()', 'onkeypress'=>'phoneMessageClear()']) }} -->
                                {{ Form::text('phone', old('phone'), ['class' => 'form-control phone', 'required' => true, 'onfocusout' => 'phoneNumberVerify()', 'onkeypress'=>'phoneMessageClear()']) }}
                                <span id="phone"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label>Country</label>
                                {{ Form::text('country','Nigeria', ['class' => 'form-control readonly', 'required' => true]) }}
                            </div>

                            <div class="form-group mb-3">
                                <label>State</label>
                                {{ Form::text('state', old('country'), ['class' => 'form-control', 'required' => true,'onkeypress'=>'return onlyCharacters(event,this)']) }}
                            </div>

                            <div class="form-group mb-3">
                                <label>City</label>
                                {{ Form::text('city', old('city'), ['class' => 'form-control', 'required' => true,'onkeypress'=>'return onlyCharacters(event,this)']) }}
                            </div>

                            <div class="form-group mb-3">
                                <label>Promo Code</label>
                                {{ Form::text('promo_code', old('promo_code'), ['class' => 'form-control']) }}
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <button class="btn btn-primary mt-2" onclick="registerToggle()">Previous</button>
                                </div>
                                <div class="col-sm-4">

                                </div>
                                <div class="col-sm-4 text-right">
                                    <button type="submit"  class="btn btn-primary mt-2">Register</button>
                                </div>
                            </div>
                        </fieldset>

                           {{-- <span class="mt-3 mb-3 line-thru text-center text-uppercase">
                            <span>or</span>
                        </span>

                            <p class="text-center">Already have an account? <a href="/login">Login!</a></p>--}}

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--lost-password-modal -->
        <div id="forgotPasswordModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <a href="/" class="logo float-left">
                            <img src="/themes/admin/img/logo-placeholder.png" height="54" alt="{{ env('APP_NAME') }}" />
                        </a>
                        <h4 class="modal-title">    <div class="card-title-sign mt-3 text-right">
                                <h2 class="title text-uppercase font-weight-bold m-0"><i class="fas fa-user mr-1"></i> Recover Password</h2>
                            </div>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <p class="m-0">Enter your e-mail below and we will send you reset instructions!</p>
                            </div>
                            <span id="reset-message"></span>

                            {!! Form::open(['url' => route('password.email'), 'class' => 'reset-password-form']) !!}

                            <div class="form-group mb-0">
                                <div class="input-group">
                                    {{ Form::text('email', old('email'), ['class' => 'form-control form-control-lg forgot-password-email', 'required' => true, 'autofocus' => true]) }}
                                    <span class="input-group-append">
                                    <button class="btn btn-primary btn-lg" type="button" onclick="forgotPasswordURL()">Reset!</button>
                                </span>
                                </div>
                            </div>

                          {{--  <p class="text-center mt-3">Remembered? <a href="#" data-toggle="modal" data-target="#loginModal">Log In!</a></p>--}}

                            {!! Form::close() !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- start: search & user box -->
    <!-- end: search & user box -->
    <!-- </header> -->

    <!-- end: header -->
