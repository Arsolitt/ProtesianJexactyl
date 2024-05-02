@extends('layouts.admin')

@section('title')
    Manager User: {{ $partner->username }}
@endsection

@section('content-header')
    <h1>{{ $partner->username }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.partners') }}">Users</a></li>
        <li class="active">{{ $partner->username }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <form action="{{ route('admin.partners.view', $partner->id) }}" method="post">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Identity</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="pUserId">Partner</label>
                            <p class="small text-muted no-margin">{{ $partner->email }}</p>
                        </div>

                        <div class="form-group">
                            <label for="partner_discount" class="control-label">Partner discount</label>
                            <div>
                                <input type="text" autocomplete="off" name="partner_discount" value="{{ $partner->partner_discount }}" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="referral_discount" class="control-label">Referral discount</label>
                            <div>
                                <input type="text" autocomplete="off" name="referral_discount" value="{{ $partner->referral_discount  }}" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="referral_commission" class="control-label">Referral reward</label>
                            <div>
                                <input type="text" autocomplete="off" name="referral_reward" value="{{ $partner->referral_reward }}" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        {!! method_field('PATCH') !!}
                        <input type="submit" value="Update Partner" class="btn btn-primary btn-sm">
                    </div>
                </div>
            </div>
        </form>
        <div class="col-xs-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Delete Partner</h3>
                </div>
                <div class="box-footer">
                    <form action="{{ route('admin.partners.view', $partner->id) }}" method="POST">
                        {!! csrf_field() !!}
                        {!! method_field('DELETE') !!}
                        <input id="delete" type="submit" class="btn btn-sm btn-danger pull-right" value="Delete Partner" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
