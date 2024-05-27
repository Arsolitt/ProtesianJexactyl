<div class="header myblur flex justify-between items-center px-5 py-2 mb-2.5">
    <a href="/"><img src="{{ config('app.logo') }}" width="48" height="48" alt="Logo" class="header_logo"></a>
    <h1 class="header_title text-center">ProtesiaN Host</h1>

    <a href="/home" class="header_button font-bold text-lg welcome-button cursor-pointer">{{ Auth::user() ? Auth::user()->username : __('Войти') }}</a>
</div>

<style>
    .header_title {
        /*background: linear-gradient(to left, #85163a, #cb2049);*/
        /*-webkit-background-clip: text;*/
        /*-webkit-text-fill-color: transparent;*/
        /*background-clip: text;*/
        /*text-fill-color: transparent;*/
        /*-moz-background-clip: text;*/
        /*-moz-text-fill-color: transparent;*/
        /*-ms-background-clip: text;*/
        /*-ms-text-fill-color: transparent;*/
        /*-o-background-clip: text;*/
        /*-o-text-fill-color: transparent;*/
        color: #27bfcc;
        font-weight: 900;
        font-size: 3rem/* 48px */;
        line-height: 1;
        text-shadow: 2px 0 #111, -2px 0 #111, 0 2px #111, 0 -2px #111,
             1px 1px #111, -1px -1px #111, 1px -1px #111, -1px 1px #111;
    }
</style>
