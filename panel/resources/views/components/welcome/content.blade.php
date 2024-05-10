@props([
    'operationTime'
])

<div class="flex min-h-full flex-1">
    <div class="flex flex-col md:grid grid-cols-3 grid-rows-3 text-center font-bold text-lg flex-1 content-center">
        <div class="cell order-2 md:order-0"><p class="welcome-button">Низкий пинг из любой точки мира</p></div>
        <div class="cell order-4 md:order-0"><p class="welcome-button">Без свапа и оверсела</p></div>
        <div class="cell order-1 md:order-0"><p class="welcome-button">Админ знает толк в серверах и кубиках</p></div>
        <div class="cell order-3 md:order-0"><p class="welcome-button">Гибкая конфигурация</p></div>

        <div class="cell">
            <p class="xs:text-3xl myblur rounded-xl p-1">
                <span>Тут можно захостить майнкрафт для игры с друзьями</span>
                <br>
                <span>Или даже поднять свой проект 0_0</span>
                <br>
                <span class="text-sm text-gray-300">И не только майнкрафт, можно развернуть любое приложение</span>
            </p>
        </div>

        <div class="cell order-6 md:order-0"><p class="welcome-button">Защита от DDOS атак</p></div>
        <div class="cell order-7 md:order-0"><p class="welcome-button">Ежедневные бэкапы</p></div>
        <div class="cell order-8 md:order-0"><p class="welcome-button">Дней работы: {{ $operationTime }}</p></div>
        <div class="cell order-5 md:order-0"><p class="welcome-button">SLA 99% доступности</p></div>

    </div>
</div>

<style>
    .cell {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    .welcome-button {

    }

    .welcome-button:hover {

    }
</style>
