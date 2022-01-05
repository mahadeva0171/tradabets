@extends('_layouts.master')

@section('main-title', 'Bank Accounts')

@section('main-content')
    @if (session('status'))
        <div class="alert alert-success">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('status') }}
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('error') }}
        </div>
    @elseif (session('errors'))
        <div class="alert alert-danger">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session('errors') }}
        </div>
    @endif
 
    <section class="card">
    
        <div class="card-body">

            <div class="col-lg-12">
                <table class="table table-responsive-lg table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                    <th class="is-status">Account Name</th>
                    <th class="is-status">Account Number</th>
                    <th class="is-status">Bank Name</th>
                    <th class="is-status">Bank Code</th>
                    <th class="is-status">BVN Number</th>
                    <th class="is-status">Active</th>
                    </thead>
                    <tbody>
                        @foreach($bank_list as $row)
                            <tr>
                                <td>{{$row->account_name}}</td>
                                <td>{{$row->account_number}}</td>
                                <td>{{$row->bank_name}}</td>
                                <td>{{$row->bank_code}}</td>
                                <td>{{$row->BVN_Number}}</td>
                                <td>{{$row->Active_status}}</td>

                            </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
        <footer class="card-footer">
            <div class="row">


                <div class="col-md-12 text-right">
                    <a href="{{ url('add-bank-account') }}" class="btn btn-primary" id="txtEdit">Add Bank Account</a>
                </div>
            </div>
        </footer>
    </section>

@endsection
