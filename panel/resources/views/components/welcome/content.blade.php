@props([
    'operationTime'
])

<div class="flex min-h-full flex-1">
    <div class="flex text-center font-bold text-lg flex-1 justify-center items-center">
{{--        <div class="cell"><p class="welcome-button">Низкий пинг из любой точки мира</p></div>--}}
{{--        <div class="cell"><p class="welcome-button">Без свапа и оверсела</p></div>--}}
{{--        <div class="cell"><p class="welcome-button">Админ знает толк в серверах и кубиках</p></div>--}}
{{--        <div class="cell"><p class="welcome-button">Гибкая конфигурация</p></div>--}}

        <div class="cell">
            <p class="text-3xl">
                <span>Тут можно создать сервер майнкрафт для игры с друзьями</span>
                <br>
                <span>Или даже поднять свой проект 0_0</span>
                <br>
                <span class="text-sm text-gray-300">И не только майнкрафт, можно развернуть любое приложение</span>
            </p>
        </div>

{{--        <div class="cell"><p class="welcome-button">Защита от DDOS атак</p></div>--}}
{{--        <div class="cell"><p class="welcome-button">Ежедневные бэкапы</p></div>--}}
{{--        <div class="cell"><p class="welcome-button">Дней работы: {{ $operationTime }}</p></div>--}}
{{--        <div class="cell"><p class="welcome-button">SLA 99% доступности</p></div>--}}

    </div>
</div>

<style>
    .cell {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
</style>
