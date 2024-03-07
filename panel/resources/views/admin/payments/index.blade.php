@extends('layouts.admin')

@section('title')
    List Users
@endsection

@section('content-header')
    <h1>Users<small>All registered users on the system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Users</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Partner List</h3>
                <div class="box-tools search01">
                    <form action="{{ route('admin.partners') }}" method="GET">
                        <div class="input-group input-group-sm">
                            <input type="text" name="filter[email]" class="form-control pull-right" value="{{ request()->input('filter.email') }}" placeholder="Search">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <a href="{{ route('admin.partners.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                            <th>External ID</th>
                            <th>Internal ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            @php
                                $bgColor = match($payment->status) {
                                    'paid' => 'darkgreen',
                                    'canceled' => 'darkred',
                                    default => 'gray'
                                };
                            @endphp
                            <tr class="align-middle">
                                <td><a href="{{ route('admin.users.view', $payment->user_id) }}">{{ $payment->username }}</a></td>
                                <td>{{ $payment->amount }}</td>
                                <td style="background-color: {{$bgColor}}; border: 1px solid transparent; text-align: center;">{{ $payment->status }}</td>
                                <td>{{ $payment->created_at }}</td>
                                <td>{{ $payment->updated_at }}</td>
                                <td>{{ $payment->external_id }}</td>
                                <td>{{ $payment->id }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $payments->appends(['query' => Request::input('query')])->render() !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
