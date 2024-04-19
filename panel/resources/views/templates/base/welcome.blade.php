@extends('templates/wrapper', [
    'css' => ['body' => 'bg-ebony-950 text-white'],
])

@section('container')
    <main>
        <x-welcome.header></x-welcome.header>
        <x-welcome.content></x-welcome.content>
        <x-welcome.footer></x-welcome.footer>
    </main>
@endsection
