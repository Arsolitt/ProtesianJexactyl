@extends('layouts.admin')
@include('partials/admin.jexactyl.nav', ['activeTab' => 'store'])

@section('title')
    Jexactyl Settings
@endsection

@section('content-header')
    <h1>ProtesiaN Store<small>Configure the ProtesiaN store.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">ProtesiaN</li>
    </ol>
@endsection

@section('content')
    @yield('jexactyl::nav')
    <div class="row">
        <div class="col-xs-12">
            <form action="{{ route('admin.jexactyl.store') }}" method="POST">
                <div class="box
                    @if($enabled == 'true')
                        box-success
                    @else
                        box-danger
                    @endif
                ">
                    <div class="box-header with-border">
                        <i class="fa fa-shopping-cart"></i>
                        <h3 class="box-title">Jexactyl Storefront <small>Configure whether certain options for the store
                                are enabled.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Storefront Enabled</label>
                                <div>
                                    <select name="store:enabled" class="form-control">
                                        <option @if ($enabled === 'false') selected @endif value="false">Disabled
                                        </option>
                                        <option @if ($enabled === 'true') selected @endif value="true">Enabled</option>
                                    </select>
                                    <p class="text-muted"><small>Determines whether users can access the store
                                            UI.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label" for="store:currency">Name of currency</label>
                                <select name="store:currency" id="store:currency" class="form-control">
                                    @foreach ($currencies as $currency)
                                        <option @if ($selected_currency === $currency['code']) selected
                                                @endif value="{{ $currency['code'] }}">{{ $currency['name'] }}</option>
                                    @endforeach
                                </select>
                                <p class="text-muted"><small>The name of the currency used for Jexactyl.</small></p>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <i class="fa fa-dollar"></i>
                        <h3 class="box-title">Yookassa <small>Конфигурация для Юкассы</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Yookassa visible name</label>
                                <div>
                                    <input type="text" class="form-control" name="store:yookassa:name"
                                           value="{{ $yookassa['name'] }}"/>
                                    <p class="text-muted"><small>Maximum Payment Amount</small>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Yookassa Enabled</label>
                                <div>
                                    <select name="store:yookassa:enabled" class="form-control">
                                        <option @if ($yookassa['enabled'] === 'false') selected @endif value="false">
                                            Disabled
                                        </option>
                                        <option @if ($yookassa['enabled'] === 'true') selected @endif value="true">
                                            Enabled
                                        </option>
                                    </select>
                                    <p class="text-muted"><small>Determines whether users can buy credits with
                                            Yookassa.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Yookassa Minimal Payment Amount</label>
                                <div>
                                    <input type="text" class="form-control" name="store:yookassa:min"
                                           value="{{ $yookassa['min'] }}"/>
                                    <p class="text-muted"><small>Minimal Payment Amount</small>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Yookassa Maximum Payment Amount</label>
                                <div>
                                    <input type="text" class="form-control" name="store:yookassa:max"
                                           value="{{ $yookassa['max'] }}"/>
                                    <p class="text-muted"><small>Maximum Payment Amount</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <i class="fa fa-dollar"></i>
                        <h3 class="box-title">Resource Pricing <small>Set specific pricing for resources.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">Cost per 1GB RAM</label>
                                <div>
                                    <input type="text" class="form-control" name="store:cost:memory"
                                           value="{{ $cost_memory }}"/>
                                    <p class="text-muted"><small>Used to calculate the total cost for 1GB of
                                            RAM.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Cost per 1GB Disk</label>
                                <div>
                                    <input type="text" class="form-control" name="store:cost:disk"
                                           value="{{ $cost_disk }}"/>
                                    <p class="text-muted"><small>Used to calculate the total cost for 1GB of
                                            disk.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Cost per 1 Server Slot</label>
                                <div>
                                    <input type="text" class="form-control" name="store:cost:slot"
                                           value="{{ $cost_slot }}"/>
                                    <p class="text-muted"><small>Used to calculate the total cost for 1 server
                                            slot.</small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Cost per 1 Network Allocation</label>
                                <div>
                                    <input type="text" class="form-control" name="store:cost:allocation"
                                           value="{{ $cost_allocation }}"/>
                                    <p class="text-muted"><small>Used to calculate the total cost for 1 port.</small>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Cost per 1 Server Backup</label>
                                <div>
                                    <input type="text" class="form-control" name="store:cost:backup"
                                           value="{{ $cost_backup }}"/>
                                    <p class="text-muted"><small>Used to calculate the total cost for 1 backup.</small>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Cost per 1 Server Database</label>
                                <div>
                                    <input type="text" class="form-control" name="store:cost:database"
                                           value="{{ $cost_database }}"/>
                                    <p class="text-muted"><small>Used to calculate the total cost for 1
                                            database.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <i class="fa fa-area-chart"></i>
                        <h3 class="box-title">Resource Min Limits <small>Set specific limits for resources.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">RAM limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:min:memory"
                                               value="{{ $limit_min_memory }}"/>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                    <p class="text-muted"><small>The minimum amount of RAM a server can be deployed
                                            with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Disk limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:min:disk"
                                               value="{{ $limit_min_disk }}"/>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                    <p class="text-muted"><small>The minimum amount of disk a server can be deployed
                                            with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Network Allocation limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:min:allocations"
                                               value="{{ $limit_min_allocations }}"/>
                                        <span class="input-group-addon">ports</span>
                                    </div>
                                    <p class="text-muted"><small>The minimum amount of ports (allocations) a server can
                                            be deployed with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Backup limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:min:backups"
                                               value="{{ $limit_min_backups }}"/>
                                        <span class="input-group-addon">backups</span>
                                    </div>
                                    <p class="text-muted"><small>The minimum amount of backups a server can be deployed
                                            with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Database limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:min:databases"
                                               value="{{ $limit_min_databases }}"/>
                                        <span class="input-group-addon">databases</span>
                                    </div>
                                    <p class="text-muted"><small>The minimum amount of databases a server can be
                                            deployed with. </small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <i class="fa fa-area-chart"></i>
                        <h3 class="box-title">Resource Max Limits <small>Set specific limits for resources.</small></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label">RAM limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:max:memory"
                                               value="{{ $limit_max_memory }}"/>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                    <p class="text-muted"><small>The maximum amount of RAM a server can be deployed
                                            with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Disk limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:max:disk"
                                               value="{{ $limit_max_disk }}"/>
                                        <span class="input-group-addon">MB</span>
                                    </div>
                                    <p class="text-muted"><small>The maximum amount of disk a server can be deployed
                                            with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Network Allocation limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:max:allocations"
                                               value="{{ $limit_max_allocations }}"/>
                                        <span class="input-group-addon">ports</span>
                                    </div>
                                    <p class="text-muted"><small>The maximum amount of ports (allocations) a server can
                                            be deployed with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Backup limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:max:backups"
                                               value="{{ $limit_max_backups }}"/>
                                        <span class="input-group-addon">backups</span>
                                    </div>
                                    <p class="text-muted"><small>The maximum amount of backups a server can be deployed
                                            with. </small></p>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Database limit</label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="store:limit:max:databases"
                                               value="{{ $limit_max_databases }}"/>
                                        <span class="input-group-addon">databases</span>
                                    </div>
                                    <p class="text-muted"><small>The maximum amount of databases a server can be
                                            deployed with. </small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! csrf_field() !!}
                <button type="submit" name="_method" value="PATCH" class="btn btn-default pull-right">Save Changes
                </button>
            </form>
        </div>
    </div>
@endsection


