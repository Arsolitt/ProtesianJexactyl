<div class="header myblur flex justify-between items-center px-5 py-2 mb-2.5">
    <a href="/"><img src="{{ config('app.logo') }}" width="48" height="48" alt="Logo" class="header_logo"></a>
    <h1 class="header_title font-black text-center text-5xl bg-gradient-to-l from-negative-900 to-negative-600 bg-clip-text text-transparent">ProtesiaN Host</h1>

    <a href="/home" class="header_button font-bold text-lg welcome-button cursor-pointer">{{ Auth::user() ? Auth::user()->username : __('Войти') }}</a>
</div>

<style>

</style>
