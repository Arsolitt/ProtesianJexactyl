@extends('layouts.admin')
@include('partials/admin.users.nav', ['activeTab' => 'resources', 'user' => $user])

@section('title')
    Resources: {{ $user->username }}
@endsection

@section('content-header')
    <h1>{{ $user->name_first }} {{ $user->name_last}}<small>{{ $user->username }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.users') }}">Users</a></li>
        <li class="{{ route('admin.users.view', ['user' => $user]) }}">{{ $user->username }}</li>
        <li class="active">Resources</li>
    </ol>
@endsection

@section('content')
    @yield('users::nav')
    <div class="row">
            <div class="col-xs-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">User Resources</h3>
                    </div>
                    <form action="{{ route('admin.users.resources', $user->id) }}" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="store_balance" class="control-label">Total balance</label>
                                    <div class="input-group">
                                        <input type="text" id="credits" value="{{ $user->credits }}" name="credits" class="form-control form-autocomplete-stop">
                                        <span class="input-group-addon">credits</span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="store_slots" class="control-label">Total Slots available</label>
                                    <div class="input-group">
                                        <input type="text" id="server_slots" value="{{ $user->server_slots }}" name="server_slots" class="form-control form-autocomplete-stop">
                                        <span class="input-group-addon">slots</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            {!! csrf_field() !!}
                            <button type="submit" name="_method" value="PATCH" class="btn btn-sm btn-primary pull-right">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
