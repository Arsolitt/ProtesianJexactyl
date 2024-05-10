<!DOCTYPE html>
<html>
    <head>
        <title>{{ config('app.name', 'ProtesiaN Host') }}</title>

        @section('meta')
            <meta property="og:title" content="{{ config('app.name', 'ProtesiaN Host') }}"/>
            <meta property="og:description" content="| Без свапа и оверсела"/>
            <meta property="og:url" content="{{ route('index') }}"/>
            <meta property="og:site_name" content="{{ config('app.name', 'ProtesiaN Host') }}"/>
            <meta property="og:image" content="{{ asset('images/preview.webp') }}"/>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <meta name="robots" content="noindex">
            <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
            <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
            <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
            <link rel="manifest" href="/favicons/manifest.json">
            <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#bc6e3c">
            <link rel="shortcut icon" href="/favicons/favicon.ico">
            <meta name="msapplication-config" content="/favicons/browserconfig.xml">
            <meta name="theme-color" content="#0e4688">
        @show

        @section('user-data')
            @if(!is_null(Auth::user()))
                <script>
                    window.JexactylUser = {!! json_encode(Auth::user()->toVueObject()) !!};
                </script>
            @endif
            @if(!empty($siteConfiguration))
                <script>
                    window.SiteConfiguration = {!! json_encode($siteConfiguration) !!};
                </script>
            @endif
            @if(!empty($storeConfiguration))
                <script>
                    window.StoreConfiguration = {!! json_encode($storeConfiguration) !!};
                </script>
            @endif
        @show

        @if(!empty($siteConfiguration['background']))
            <style>
                body {
                    background-image: url({!! $siteConfiguration['background'] !!});
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                    background-size: cover;
                }
            </style>
        @endif

        <style>
            @import url('//fonts.googleapis.com/css?family=Rubik:300,400,500&display=swap');
            @import url('//fonts.googleapis.com/css?family=IBM+Plex+Mono|IBM+Plex+Sans:500&display=swap');
        </style>
        <style>
            .info-header1 {
                font-size: 1.875rem/* 30px */;
                line-height: 2.25rem/* 36px */;
                font-weight: 700;
                text-align: center;
            }
            .info-header2 {
                font-size: 1.5rem/* 24px */;
                line-height: 2rem/* 32px */;
                font-weight: 700;
            }
            .info-header3 {
                font-size: 1.25rem/* 20px */;
                line-height: 1.75rem/* 28px */;
                font-weight: 700;
            }
            .welcome-button {
                border: 1px transparent;
                padding: 10px;
                border-radius: 15px;
                background: linear-gradient(to right, #208a9a 50%, #235c67 50%);
                background-size: 200% 100%;
                background-position: 0% 0%;
                cursor: grab;
                transition: background-position 1s;
            }
            .welcome-button:hover {
                background-position: -100% 0%;
            }
        </style>

        @yield('assets')

        @include('layouts.scripts')
    </head>
    <body @if(isset($css['body'])) class="{{ $css['body'] }}" @endif>
        @section('content')
            @yield('above-container')
            @yield('container')
            @yield('below-container')
        @show
        @section('scripts')
            {!! $asset->js('main.js') !!}
        @show
    </body>
</html>
