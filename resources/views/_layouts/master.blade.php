<!doctype html>
<html class="fixed _sidebar-left-collapsed">
<head>

	@include('_includes.html-header')


</head>
<body>
 
	@include('_includes.body-header')

	@php 
	$kyc_status=Session::get('kyc_status');
	$account_status=Session::get('account_status');

	@endphp

<!--         <div class="row">
	        <div class="col-md-12">
	        @if(auth()->user()->role!='admin')
				@if(request()->segment(1) != 'kyc-upload-form')
                    @if($kyc_status == 0)
                        <div>
                        <p class='kyc-message'>Please Verify your kyc to withdraw your winnings.</p><a href="/kyc-upload-form"><button class="btn btn-sm btn-primary" href="/kyc-upload-form" value="verify_kyc">
                                    Verify kyc
                                </button></a>
                        </div>
                    @endif
                @endif
             @endif
            </div>
            <div class="col-md-12">
            @if(auth()->user()->role!='admin')
				@if(request()->segment(1) == 'withdraw')
                    @if($account_status == 0)
                        <div>
                        <p class='kyc-message'>Please add account details to withdraw your winnings.</p><a href="/kyc-upload-form"><button class="btn btn-sm btn-primary" href="/kyc-upload-form" value="verify_kyc">
                                    Add account
                                </button></a>
                        </div>
                    @endif
                @endif
             @endif
            </div>
        </div> -->

			@if(auth()->user()->role!='admin')
<!-- 				@if(request()->segment(1) != 'kyc-upload-form')
                    @if($kyc_status == 0)
                        <div>
                        <p class='kyc-message'>Please Verify your kyc to withdraw your winnings.</p><a href="/kyc-upload-form"><button class="btn btn-sm btn-primary" href="/kyc-upload-form" value="verify_kyc">
                                    Verify kyc
                                </button></a>
                        </div>
                    @endif
                @endif -->
                @if(request()->segment(1) != 'add-bank-account')
                    @if($account_status == 0)
                        <div>
                        <p class='kyc-message'>Please Add Bank details to withdraw your winnings.</p><a href="/add-bank-account"><button class="btn btn-sm btn-primary" href="/add-bank-account" value="Add_Bank_Account">
                                    Add Bank Account
                                </button></a>
                        </div>
                    @endif
                @endif
            @endif

        @yield('main-content')

	@include('_includes.body-footer')

	@include('_includes.html-footer')

</body>
</html>
