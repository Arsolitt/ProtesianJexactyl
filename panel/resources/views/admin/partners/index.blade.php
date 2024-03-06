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
                            <th>Partner discount</th>
                            <th>Referral discount</th>
                            <th>Referral reward</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($partners as $partner)
                            <tr class="align-middle">
                                <td><a href="{{ route('admin.partners.view', $partner->id) }}">{{ $partner->username }}</a></td>
                                <td>{{ $partner->partner_discount }}</td>
                                <td>{{ $partner->referral_discount }}</td>
                                <td>{{ $partner->referral_reward }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($partners->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $partners->appends(['query' => Request::input('query')])->render() !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
