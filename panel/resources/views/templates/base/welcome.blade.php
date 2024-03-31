@extends('templates/wrapper', [
    'css' => ['body' => 'bg-ebony-950 text-white'],
])

@section('container')
    <main>
        <x-welcome.hero></x-welcome.hero>
        <x-welcome.about></x-welcome.about>
        <x-welcome.calculator></x-welcome.calculator>
        <x-welcome.faq></x-welcome.faq>
        <x-welcome.footer></x-welcome.footer>
    </main>
@endsection
