<?php

/**
 * Contains all of the translation strings for different activity log
 * events. These should be keyed by the value in front of the colon (:)
 * in the event name. If there is no colon present, they should live at
 * the top level.
 */
return [
    'auth' => [
        'fail' => 'Не удалось войти в систему',
        'success' => 'Вошёл в систему',
        'password-reset' => 'Сброс пароля',
        'reset-password' => 'Запрос на сброс пароля',
        'checkpoint' => 'Требуется двухфакторная аутентификация',
        'recovery-token' => 'Использованный токен двухфакторного восстановления',
        'token' => 'Решение двухфакторной задачи',
        'ip-blocked' => 'Заблокирован запрос с незарегистрированного IP-адреса для :identifier',
        'sftp' => [
            'fail' => 'Не удалось войти в систему по протоколу SFTP',
        ],
    ],
    'oAuth' => [
        'success' => [
            'discord' => 'Вошёл через Discord',
            'google' => 'Вошёл через Google',
            'github' => 'Вошёл через GitHub',
            'telegram' => 'Вошёл через Telegram',
        ],
    ],
    'user' => [
        'account' => [
            'email-changed' => 'Изменена электронная почта с :old на :new',
            'password-changed' => 'Изменен пароль',
            'username-changed' => 'Изменено имя пользователя с :old на :new',
        ],
        'api-key' => [
            'create' => 'Создан новый API-ключ :identifier',
            'delete' => 'Удалён API-ключ :identifier',
        ],
        'ssh-key' => [
            'create' => 'Добавлен SSH-ключ :fingerprint',
            'delete' => 'Удалён SSH-ключ :fingerprint',
        ],
        'two-factor' => [
            'create' => 'Включена двухфакторная аутентификация',
            'delete' => 'Отключена двухфакторная аутентификация',
        ],
        'store' => [
            'resource-purchase' => 'Был приобретен ресурс',
        ],
    ],

    'server' => [
        'reinstall' => 'Переустановка сервера',
        'console' => [
            'command' => 'Выполнено ":command" на сервере',
        ],
        'power' => [
            'start' => 'Запуск сервера',
            'stop' => 'Остановка сервера',
            'restart' => 'Рестарт сервера',
            'kill' => 'Сервер принудительно остановлен',
        ],
        'backup' => [
            'download' => 'Скачал бэкап :name',
            'delete' => 'Удалил бэкап :name',
            'restore' => 'Восстановил бэкап :name backup (удалённые файлы: :truncate)',
            'restore-complete' => 'Завершено восстановление из бэкапа :name',
            'restore-failed' => 'Не удалось завершить восстановление из бэкапа :name',
            'start' => 'Создание нового бэкапа :name',
            'complete' => 'Создание нового бэкапа :name завершено',
            'fail' => 'Создание нового бэкапа :name backup не удалось',
            'lock' => 'Заблокирован бэкап :name',
            'unlock' => 'Разблокирован бэкап :name',
        ],
        'database' => [
            'create' => 'Создана новая база данных :name',
            'rotate-password' => 'Смена пароля к базе данных :name',
            'delete' => 'Удалена база данных :name',
        ],
        'file' => [
            'compress_one' => 'Архивировал :directory:file',
            'compress_other' => 'Архивировал :count файлов в :directory',
            'read' => 'Просмотрел файл :file',
            'copy' => 'Создал копию файла :file',
            'create-directory' => 'Создал папку :directory:name',
            'decompress' => 'Разархивировал :files в :directory',
            'delete_one' => 'Удалил :directory:files.0',
            'delete_other' => 'Удалил :count файлов в :directory',
            'delete' => 'Удалил :count файлов в :directory',
            'download' => 'Скачал файл :file',
            'pull' => 'Загрузил файл по ссылке :url в :directory',
            'rename_one' => 'Переименовал :directory:files.0.from в :directory:files.0.to',
            'rename_other' => 'Переименовал :count файлов в :directory',
            'write' => 'Изменил содержимое файла :file',
            'upload' => 'Начал загрузку файла',
            'uploaded' => 'Загрузил :directory:file',
        ],
        'sftp' => [
            'denied' => 'Блокировка доступа к SFTP из-за прав доступа',
            'create_one' => 'Создал :files.0',
            'create_other' => 'Создал :count новых файлов',
            'write_one' => 'Изменил содержимое :files.0',
            'write_other' => 'Изменил содержимое :count файлов',
            'delete_one' => 'Удалил :files.0',
            'delete_other' => 'Удалил :count файлов',
            'create-directory_one' => 'Создал папку :files.0',
            'create-directory_other' => 'Создал :count папок',
            'rename_one' => 'Переименовал :files.0.from в :files.0.to',
            'rename_other' => 'Переименовал или переместил :count файлов',
        ],
        'allocation' => [
            'create' => 'Добавил порт :allocation на сервер',
            'notes' => 'Обновил примечания к :allocation с ":old" на ":new"',
            'primary' => 'Установил порт :allocation основным для сервера',
            'delete' => 'Удалил порт :allocation',
        ],
        'schedule' => [
            'create' => 'Создал задачу :name',
            'update' => 'Обновил задачу :name',
            'execute' => 'Вручную запустил задачу :name',
            'delete' => 'Удалил задачу :name',
        ],
        'task' => [
            'create' => 'Добавил новое действие ":action" для задачи :name',
            'update' => 'Обновил действие ":action" для задачи :name',
            'delete' => 'Удалил действие для задачи :name',
        ],
        'settings' => [
            'rename' => 'Переименовал сервер с :old на :new',
            'description' => 'Изменил описание сервера с :old на :new',
        ],
        'startup' => [
            'edit' => 'Изменил переменную :variable с ":old" н ":new"',
            'image' => 'Изменил образ для запуска сервера с :old на :new',
        ],
        'subuser' => [
            'create' => 'Добавил пользователя :email',
            'update' => 'Обновил права для пользователя :email',
            'delete' => 'Удалил пользователя :email',
        ],
    ],
];
