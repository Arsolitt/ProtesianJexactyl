@extends('layouts.admin')
@include('partials/admin.jexactyl.nav', ['activeTab' => 'registration'])

@section('title')
    Jexactyl Settings
@endsection

@section('content-header')
    <h1>User Registration<small>Configure settings for user registration on Jexactyl.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Jexactyl</li>
    </ol>
@endsection

@section('content')
@yield('jexactyl::nav')
    <div class="row">
        <div class="col-xs-12">
            <form action="{{ route('admin.jexactyl.registration') }}" method="POST">
                <div class="box
                @if($enabled == 'true') box-success @else box-danger @endif">
                    <div class="box-header with-border">
                        <i class="fa fa-at"></i> <h3 class="box-title">Registration via Email <small>The settings for Email registration and logins.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Enabled</label>
                                <div>
                                    <select name="registration:enabled" class="form-control">
                                        <option @if ($enabled == 'false') selected @endif value="false">Disabled</option>
                                        <option @if ($enabled == 'true') selected @endif value="true">Enabled</option>
                                    </select>
                                    <p class="text-muted"><small>Determines whether people can register an account using email.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Email Verification</label>
                                <div>
                                    <select name="registration:verification" class="form-control">
                                        <option @if ($verification == 'false') selected @endif value="false">Disabled</option>
                                        <option @if ($verification == 'true') selected @endif value="true">Enabled</option>
                                    </select>
                                    <p class="text-muted"><small>Determines whether people need to verify their email to create servers.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box @if($discord_enabled == 'true') box-success @else box-danger @endif">
                    <div class="box-header with-border">
                        <i class="fa fa-comments-o"></i> <h3 class="box-title">Registration via Discord <small>The settings for Discord registration and logins.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Enabled</label>
                                <div>
                                    <select name="discord:enabled" class="form-control">
                                        <option @if ($discord_enabled == 'false') selected @endif value="false">Disabled</option>
                                        <option @if ($discord_enabled == 'true') selected @endif value="true">Enabled</option>
                                    </select>
                                    @if($discord_enabled != 'true')
                                        <p class="text-danger">People will not be able to sign up OR login with Discord if this is disabled!</p>
                                    @else
                                        <p class="text-muted"><small>Determines whether people can register an account using Discord.</small></p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Discord Client ID</label>
                                <div>
                                    <input type="text" class="form-control" name="discord:id" value="{{ $discord_id }}" />
                                    <p class="text-muted"><small>The client ID for your OAuth application. Typically 17-20 digits long.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Discord Client Secret</label>
                                <div>
                                    <input type="password" class="form-control" name="discord:secret" value="{{ $discord_secret }}" />
                                    <p class="text-muted"><small>The client secret for your OAuth application. Treat this like a password.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <i class="fa fa-microchip"></i> <h3 class="box-title">Default Resources <small>The default resources assigned to a user on registration.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Slots Amount</label>
                                <div>
                                    <input type="text" class="form-control" name="registration:slots" value="{{ $slot }}" />
                                    <p class="text-muted"><small>The amount of server slots that should be given to a user on signup.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! csrf_field() !!}
                <button type="submit" name="_method" value="PATCH" class="btn btn-default pull-right">Save Changes</button>
            </form>
        </div>
    </div>
@endsection
