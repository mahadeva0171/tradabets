<!doctype html>
<html class="fixed _sidebar-left-collapsed">
<head>

	@include('_includes.html-header')


</head>
<body>
 
	@include('_includes.body-header')

	@php 
	$kyc_status=Session::get('kyc_status');
	@endphp


	@if(auth()->user()->role!='admin')
		@if(request()->segment(1) != 'kyc-upload-form')
                    @if($kyc_status == 0)
                        <p class='kyc-message'>Please Verify your kyc to withdraw your winnings.</p>
                        <div>
                                <a href="/kyc-upload-form"><button class="btn btn-sm btn-primary" href="/kyc-upload-form" value="verify_kyc">
                                    Verify kyc
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
