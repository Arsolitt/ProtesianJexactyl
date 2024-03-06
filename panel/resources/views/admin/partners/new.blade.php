@extends('layouts.admin')

@section('title')
    Create User
@endsection

@section('content-header')
    <h1>Create Partner</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.partners') }}">Partners</a></li>
        <li class="active">Create</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <form method="post">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Identity</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="pUserId">Partner</label>
                        <select id="pUserId" name="user_id" class="form-control" style="padding-left:0;"></select>
                        <p class="small text-muted no-margin">Email address of the Partner.</p>
                    </div>

                    <div class="form-group">
                        <label for="partner_discount" class="control-label">Partner discount</label>
                        <div>
                            <input type="text" autocomplete="off" name="partner_discount" value="{{ old('partner_discount') }}" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referral_discount" class="control-label">Referral discount</label>
                        <div>
                            <input type="text" autocomplete="off" name="referral_discount" value="{{ old('referral_discount') }}" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referral_reward" class="control-label">Referral reward</label>
                        <div>
                            <input type="text" autocomplete="off" name="referral_reward" value="{{ old('referral_reward') }}" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    {!! csrf_field() !!}
                    <input type="submit" value="Create Partner" class="btn btn-success btn-sm">
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script type="application/javascript">
        function initUserIdSelect(data) {
            function escapeHtml(str) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            $('#pUserId').select2({
                ajax: {
                    url: '/admin/users/accounts.json',
                    dataType: 'json',
                    delay: 250,

                    data: function (params) {
                        return {
                            filter: { email: params.term },
                            page: params.page,
                        };
                    },

                    processResults: function (data, params) {
                        return { results: data };
                    },

                    cache: true,
                },

                data: data,
                escapeMarkup: function (markup) { return markup; },
                minimumInputLength: 2,
                templateResult: function (data) {
                    if (data.loading) return escapeHtml(data.text);

                    return '<div class="user-block"> \
                <img class="img-circle img-bordered-xs" src="https://www.gravatar.com/avatar/' + escapeHtml(data.md5) + '?s=120" alt="User Image"> \
                <span class="username"> \
                    <a href="#">' + escapeHtml(data.name_first) + ' ' + escapeHtml(data.name_last) +'</a> \
                </span> \
                <span class="description"><strong>' + escapeHtml(data.email) + '</strong> - ' + escapeHtml(data.username) + '</span> \
            </div>';
                },
                templateSelection: function (data) {
                    return '<div> \
                <span> \
                    <img class="img-rounded img-bordered-xs" src="https://www.gravatar.com/avatar/' + escapeHtml(data.md5) + '?s=120" style="height:28px;margin-top:-4px;" alt="User Image"> \
                </span> \
                <span style="padding-left:5px;"> \
                    ' + escapeHtml(data.name_first) + ' ' + escapeHtml(data.name_last) + ' (<strong>' + escapeHtml(data.email) + '</strong>) \
                </span> \
            </div>';
                }

            });
        }

        $(document).ready(function() {
            // Persist 'Server Owner' select2
            @if (old('user_id'))
            $.ajax({
                url: '/admin/users/accounts.json?user_id={{ old('user_id') }}',
                dataType: 'json',
            }).then(function (data) {
                initUserIdSelect([ data ]);
            });
            @else
            initUserIdSelect();
            @endif
        });
    </script>
@endsection
