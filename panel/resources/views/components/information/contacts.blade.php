@extends('templates/wrapper', [
    'css' => ['body' => 'bg-ebony-950 text-white'],
])

@section('container')
    <main>
        <x-welcome.header></x-welcome.header>
        <div class="text-lg w-5/12 mx-auto">
            <h1 class="info-header1">Контакты ProtesiaN Hosting</h1>
            <br>
            <span>ИП Севастьянов К.А.</span>
            <br>
            <span>ОГРНИП 321547600119753</span>
            <br>
            <span>Email: arsolitt@gmail.com</span>
            <br>
            <span>Discord: Arsolitt</span>
            <br>
            <span>Telegram: @Arsolitt</span>
            <br>
            <br>
            <h2 class="info-header2">Отказ от ответственности</h2>
            <br>
            <h3 class="info-header3">Ответственность за содержание</h3>
            <p>Содержание наших страниц создавалось с особой тщательностью. Однако мы не можем гарантировать точность,
                полноту и актуальность содержания. В соответствии с законодательными положениями мы несем
                ответственность за собственное содержание этих веб-страниц. В связи с этим просим учесть, что мы не
                обязаны отслеживать передаваемую или сохраняемую информацию третьих лиц, а также расследовать
                обстоятельства, указывающие на противоправную деятельность. При этом наши обязательства по удалению или
                блокированию использования информации остаются неизменными.</p>
            <br>
            <h3 class="info-header3">Ответственность за ссылки</h3>
            <p>Наши предложения содержат ссылки на внешние сайты третьих лиц. Мы не имеем никакого влияния на содержание
                этих сайтов и поэтому не несем никакой ответственности за их постороннее содержание. Ответственность за
                содержание страниц, на которые ведут ссылки, всегда несет соответствующий поставщик или оператор
                страниц. При размещении ссылок на страницы, на которые даны ссылки, была проведена проверка на предмет
                возможных нарушений законодательства. Незаконное содержание на момент размещения ссылок не было
                обнаружено. Однако постоянный контроль содержания страниц, на которые ведут ссылки, нецелесообразен без
                конкретных доказательств нарушения закона. Если нам станет известно о каких-либо нарушениях, мы
                незамедлительно удалим такие ссылки.</p>
        </div>
    </main>
@endsection