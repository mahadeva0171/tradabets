@extends('_layouts.master')

@section('main-title', 'Withdraw')

@section('main-content')

    <section class="card">

        <div class="card-body">

            <div class="col-lg-6">

                {!! Form::open(['url' => '/withdraw-request','class' => 'form-horizontal is-dashboard-filter-form', 'method' => 'get', 'onsubmit'=>'return amountValidation()']) !!}

                {{ Form::text_md6('Amount','withdraw_amount','', ['class' => 'form-control withdraw-amount-input', 'required' => true,'onkeypress'=>'return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57','onkeyup'=>'amountValidationMessageClear()']) }}

                <div class="row form-view-row">
                    <div class="col-xs-4 col-md-4 fnt-b"></div>
                    <div class="col-xs-8 col-md-8"><span class="amount-validation-message"></span></div>
                </div>

                <div class="text-right p-1">
                    @php $kyc_status=Session::get('kyc_status'); @endphp
                    @php $account_status=Session::get('account_status'); @endphp

                {{ Form::hidden('kycstatus', $kyc_status, ['id' => 'kycstatus']) }}
                {{ Form::hidden('accountstatus', $account_status, ['id' => 'accountstatus']) }}
                {{ Form::submit('Request', ['class' => 'btn btn-sm btn-primary']) }}
                  <!-- {{ ($kyc_status == 1)?  Form::submit('Request', ['class' => 'btn btn-sm btn-primary']) :   Form::submit('Request', ['class' => 'btn btn-sm btn-primary','disabled']) }} -->

                </div>
                <div class="row form-view-row">
                    <div class="col-xs-4 col-md-4 fnt-b"></div>
                    <div class="col-xs-8 col-md-8"><span class="kyc-status-message"></span></div>
                </div>
                {!! Form::close() !!}
                
            </div>
            <div class="col-lg-6">
                <div class="row form-view-row">
                    <div class="col-xs-4 col-md-4 fnt-b">Available Balance:</div>
                    <div class="col-xs-8 col-md-8"><label class="balance-amount">{{$avail_balance}}</label></div>
                </div>
                {{--@include('_components.form-elements.text-view-md6', ['label' => 'Available Balance', 'value' =>$avail_balance,)--}}
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
