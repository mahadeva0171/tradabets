@extends('_layouts.master')

@section('main-title', 'Bank Accounts')

@section('main-content')

    {!! Form::open(['url' => '/add_account', 'method'=>'POST', 'class' => 'form-horizontal', 'files'=>true, 'onsubmit'=>'return checkAccountExist()']) !!}

    <section class="card">
			<h3><b>Add Bank Account</b></h3>

        <div class="card-body">

            {{--     @include('_components/tabs/top', ['tab_link_arr' => $tab_link_arr, 'active' => 'Details'])    --}}

            <div class="row">
                <div class="col-md-12 col-lg-6">

                    {{ Form::text_md6('Account Name:', 'form[account_name]', old('form[account_name]'), ['required' => true, 'autofocus' => true]) }}

                    {{ Form::text_md6('Account Number:', 'form[account_number]', old('form[account_number]'), ['required' => true]) }}

                    {{ Form::text_md6('Bank Name:', 'form[bank_name]', old('form[bank_name]'), ['required' => true]) }}

                    {{ Form::text_md6('Bank Code:', 'form[bank_code]', old('form[bank_code]'), ['required' => true]) }}

                    <!-- {{ Form::text_md6('BVN Number:', 'form[bvn_number]', old('form[bvn_number]'), ['required' => true]) }} -->

                </div>

            </div> <!-- /row -->

            @include('_components.tabs.btm')

        </div>

        <footer class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    <span class="account-message">{{ @$error }}</span>
                </div>


                <div class="col-md-6 text-right">
                    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                </div>
            </div> <!-- /row -->
        </footer>

    </section>

    {!! Form::close() !!}

@endsection
