@extends('_layouts.master')

@section('main-title', 'Withdraw Request List')

@section('main-content')

    <section class="card">

        <div class="card-body">

            <div class="col-lg-12">
                <table class="table table-responsive-lg table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                    <th class="is-status">Name</th>
                    <th class="is-status">Amount</th>
                    <th class="is-status">Status</th>
                    <th class="is-status">Date</th>
                    <th class="is-status">Action</th>
                    </thead>
                    <tbody>
                    @foreach($withdraw_requests as $row)
                        <tr>
                            <td>{{$row->user->first_name}} {{$row->user->last_name}}</td>
                            <td>{{$row->amount}}</td>
                            <td>{{$row->status_description}}</td>
                            <td>{{$row->created_at}}</td>
                            <td>
                                <a href="/withdraw-request/view/{{$row->id}}">View Details</a></td>
                        </tr>
                    @endforeach
                    </tbody>
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
