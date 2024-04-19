<div class="header flex justify-between items-center px-5 py-2 min-h-[12dvh]">
    <a href="/"><img src="{{ config('app.logo') }}" width="48" height="48" alt="Logo" class="header_logo"></a>
    <h1 class="header_title font-bold text-center text-4xl bg-gradient-to-l from-main-400 to-main-600 bg-clip-text text-transparent">ProtesiaN Host</h1>

    <a href="/home" class="header_button font-bold text-lg border-transparent rounded-lg p-2 bg-main-600 hover:bg-menuActive-500 cursor-pointer">{{ Auth::user() ? Auth::user()->username : __('Войти') }}</a>
</div>
