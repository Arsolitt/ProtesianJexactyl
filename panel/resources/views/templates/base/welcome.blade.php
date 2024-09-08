@extends('templates/wrapper', [
    'css' => ['body' => 'bg-ebony-950 text-white'],
])

@section('container')
    <main class="welcome-container flex flex-col">
        <x-welcome.header></x-welcome.header>
        <x-welcome.content operationTime="{{ $operationTime }}"></x-welcome.content>
        <x-welcome.footer></x-welcome.footer>
    </main>
    <style>
        .welcome-container {
            background-image: url({{ asset('images/background.webp') }});
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }
    </style>
@endsection
