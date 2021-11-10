@extends('_layouts.master')

@section('main-title', 'Deposit')

@section('main-content')



    <section class="card">

        <div class="card-body">

            <div class="col-lg-6">





                {!! Form::open(['url' => '/payment-request','id' => 'paypalForm','class' => 'form-horizontal is-dashboard-filter-form', 'method' => 'get','onsubmit'=>'return depositAmountValidation()']) !!}


                {{ Form::text_md6('Amount','deposit_amount',$request_amount, ['class' => 'form-control deposit_amount','id'=>'deposit_block', 'required' => true,'onkeypress'=>'return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57', 'onkeyup'=>'amountValidationMessageClear()','maxlength'=>7]) }}


                <div class="row form-view-row">
                    <div class="col-xs-4 col-md-4 fnt-b"></div>
                    <div class="col-xs-8 col-md-8"><span class="amount-validation-message"></span></div>
                </div>



{{--                <div class="text-right p-1">--}}
{{--                    {{ Form::submit('Deposit with Paypal', ['class' => 'btn btn-sm btn-primary']) }}--}}
{{--                </div>--}}


                {!! Form::close() !!}

                                <br>
                                <p>Choose Payment method</p>
                                <input class="radio" type="radio" name="payment" id="paystk" value="paystack" checked /> <span>Paystack</span><br>
                                <input class="radio" type="radio" name="payment" id="paypl" value="paypal" /> <span>PaypaL</span>
                                <br><br>
                             <div class="text-right p-1">
                                <button class="btn btn-sm btn-primary" onclick="depositclick();" value="Deposit">
                                    Deposit
                                </button>
                             </div>
                

                {{--                 PAYSTACK--}}
                                <form method="POST" onsubmit="paystackFunction()" action="{{ route('pay') }}" id="paymentForm" accept-charset="UTF-8" class="form-horizontal" role="form">
                                    <div class="row" style="margin-bottom:0px;">
                                        <div class="col-md-12 col-md-offset-2">

                                            <input type="hidden" name="email" value="tradabets@test.com">
                                            <input type="hidden" name="orderID" value="">
                                            <input type="hidden" name="amount" id="paystack_amount" value="">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="currency" value="NGN">
                                            <input type="hidden" name="metadata" value="{{ json_encode($array = ['key_name' => 'value',]) }}">
                                            <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                                            {{ csrf_field() }}

                                            <input type="hidden" name="metadata" value="{{ json_encode($array = [ 'amount' => auth::user()->amount ]) }}">

                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

{{--                                            <div class="text-right p-1">--}}
{{--                                                <button class="btn btn-sm btn-primary" type="submit" value="Deposit with Paystack!">--}}
{{--                                                    Deposit with Paystack--}}
{{--                                                </button>--}}
{{--                                            </div>--}}
                                        </div>
                                    </div>
                                </form>


{{--                                            PAYSTACK--}}


            </div>

            <div class="col-lg-6">
                @include('_components.form-elements.text-view-md6', ['label' => 'Available Balance', 'value' =>$avail_balance])
            </div>
        </div>

        <!-- <footer class="card-footer">
          <div class="row">
        <div class="col-md-6">
                <a href="/forms/area" class="btn btn-default">Cancel</a>
        </div>
        <div class="col-md-6 text-right">
                <a href="#" class="btn btn-primary" id="txtEdit">Add</a>
        </div>
    </div>

        </footer> -->
    </section>

@endsection
