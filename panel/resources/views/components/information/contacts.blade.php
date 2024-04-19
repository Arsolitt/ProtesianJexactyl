@extends('templates/wrapper', [
    'css' => ['body' => 'bg-ebony-950 text-white'],
])

@section('container')
    <main>
        <x-welcome.header></x-welcome.header>
        Контакты
    </main>
@endsection
