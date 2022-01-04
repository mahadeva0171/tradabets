@extends('_layouts.master')

@section('main-title', 'Withdraw Request List')

@section('main-content')

    <section class="card">

        <div class="card-body">

            <div class="col-lg-12">
              <form id="withdraw-request-lists-form" method="POST">
                <table class="table table-responsive-lg table-bordered table-striped mb-0 withdraw_table" id="datatable-default" >
                    <thead>
                            <th class="bulkCheckbox nummer"><input type="checkbox" name="checkProducts" onclick="checkAll('#datatable-default', this)" />Select All</th>
                            <th class="is-status">Name</th>
                            <th class="is-status">Amount</th>
                            <th class="is-status">Status</th>
                            <th class="is-status">Date</th>
                            <th class="is-status">Action</th>
                            <th><div class="approve_reject_all"><input class="btn btn-primary" style="font-size: smaller;" type="button" value="Approve Selected" onclick="getSelectedCheckboxes()">
                                <input class="btn btn-danger" style="font-size: smaller;" type="button" value="Reject Selected" onclick=""></div></th>
                    </thead>
                    
                    <tbody>
                        @foreach($withdraw_requests as $row)
                            <tr>
                                <td><input class="bulkCheckbox" type="checkbox" name="select_request" class="select_request" value="{{$row->id}}"/></td>
                                <td>{{$row->user->first_name}} {{$row->user->last_name}}</td>
                                <td>{{$row->amount}}</td>
                                <td>{{$row->status_description}}</td>
                                <td>{{$row->created_at}}</td>

                                <td><a href="/withdraw-request/view/{{$row->id}}">View Details</a></td>

                                <td>
                                <div class="approve_reject"><input class="btn btn-primary" style="font-size: smaller;" type="button" value="Approve" onclick="location.href='/initiate_transaction/{{$row->id}}'">
                                     <input class="btn btn-danger" style="font-size: smaller;" type="button" value="Reject" onclick="location.href='/withdraw-request-individual-reject/update/{{$row->id}}'">
                                </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

              </form>
            </div>
        </div>

    </section>

@endsection
