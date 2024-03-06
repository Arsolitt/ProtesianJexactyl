<?php

namespace Jexactyl\Models;

use Illuminate\Support\Collection;

class Permission extends Model
{
    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    public const RESOURCE_NAME = 'subuser_permission';

    /**
     * Constants defining different permissions available.
     */
    public const ACTION_WEBSOCKET_CONNECT = 'websocket.connect';
    public const ACTION_CONTROL_CONSOLE = 'control.console';
    public const ACTION_CONTROL_START = 'control.start';
    public const ACTION_CONTROL_STOP = 'control.stop';
    public const ACTION_CONTROL_RESTART = 'control.restart';

    public const ACTION_DATABASE_READ = 'database.read';
    public const ACTION_DATABASE_CREATE = 'database.create';
    public const ACTION_DATABASE_UPDATE = 'database.update';
    public const ACTION_DATABASE_DELETE = 'database.delete';
    public const ACTION_DATABASE_VIEW_PASSWORD = 'database.view_password';

    public const ACTION_SCHEDULE_READ = 'schedule.read';
    public const ACTION_SCHEDULE_CREATE = 'schedule.create';
    public const ACTION_SCHEDULE_UPDATE = 'schedule.update';
    public const ACTION_SCHEDULE_DELETE = 'schedule.delete';

    public const ACTION_USER_READ = 'user.read';
    public const ACTION_USER_CREATE = 'user.create';
    public const ACTION_USER_UPDATE = 'user.update';
    public const ACTION_USER_DELETE = 'user.delete';

    public const ACTION_BACKUP_READ = 'backup.read';
    public const ACTION_BACKUP_CREATE = 'backup.create';
    public const ACTION_BACKUP_DELETE = 'backup.delete';
    public const ACTION_BACKUP_DOWNLOAD = 'backup.download';
    public const ACTION_BACKUP_RESTORE = 'backup.restore';

    public const ACTION_ALLOCATION_READ = 'allocation.read';
    public const ACTION_ALLOCATION_CREATE = 'allocation.create';
    public const ACTION_ALLOCATION_UPDATE = 'allocation.update';
    public const ACTION_ALLOCATION_DELETE = 'allocation.delete';

    public const ACTION_FILE_READ = 'file.read';
    public const ACTION_FILE_READ_CONTENT = 'file.read-content';
    public const ACTION_FILE_CREATE = 'file.create';
    public const ACTION_FILE_UPDATE = 'file.update';
    public const ACTION_FILE_DELETE = 'file.delete';
    public const ACTION_FILE_ARCHIVE = 'file.archive';
    public const ACTION_FILE_SFTP = 'file.sftp';

    public const ACTION_STARTUP_READ = 'startup.read';
    public const ACTION_STARTUP_UPDATE = 'startup.update';
    public const ACTION_STARTUP_DOCKER_IMAGE = 'startup.docker-image';

    public const ACTION_SETTINGS_RENAME = 'settings.rename';
    public const ACTION_SETTINGS_REINSTALL = 'settings.reinstall';

    public const ACTION_ACTIVITY_READ = 'activity.read';

    /**
     * Should timestamps be used on this model.
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected $table = 'permissions';

    /**
     * Fields that are not mass assignable.
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Cast values to correct type.
     */
    protected $casts = [
        'subuser_id' => 'integer',
    ];

    public static array $validationRules = [
        'subuser_id' => 'required|numeric|min:1',
        'permission' => 'required|string',
    ];

    /**
     * All the permissions available on the system. You should use self::permissions()
     * to retrieve them, and not directly access this array as it is subject to change.
     *
     * @see \Jexactyl\Models\Permission::permissions()
     */
    protected static array $permissions = [
        'websocket' => [
            'description' => 'Даёт возможность пользователю подключаться к серверу через вебсокет',
            'keys' => [
                'connect' => 'Разрешает видеть вывод консоли.',
            ],
        ],

        'control' => [
            'description' => 'Даёт возможность изменить состояние сервера и отправлять команды в консоль.',
            'keys' => [
                'console' => 'Разрешает отправлять команды в консоль.',
                'start' => 'Разрешает включать сервер, если он выключен.',
                'stop' => 'Разрешает выключать сервер, если он включен.',
                'restart' => 'Разрешает перезапускать сервер. Он сможет включить его, даже если он выключен. Но не сможет выключить его окончательно.',
            ],
        ],

        'user' => [
            'description' => 'Даёт возможность управлять другими пользователями. Не сможет изменить права, к которые не имеет сам.',
            'keys' => [
                'create' => 'Разрешает приглашать новых пользователей.',
                'read' => 'Разрешает смотреть разрешения других пользователей.',
                'update' => 'Разрешает изменять разрешения других пользователей.',
                'delete' => 'Разрешает удалять пользователей с сервера.',
            ],
        ],

        'file' => [
            'description' => 'Даёт возможность управлять файлами на сервере',
            'keys' => [
                'create' => 'Разрешает создавать или загружать новые файлы.',
                'read' => 'Разрешает просматривать папки, но не разрешает читать содержимое файлов или скачивать их.',
                'read-content' => 'Разрешает просматривать содержимое файлов и скачивать их.',
                'update' => 'Разрешает изменять содержимое файлов.',
                'delete' => 'Разрешает удалять файлы и папки.',
                'archive' => 'Разрешает архивировать и разархивировать файлы и папки.',
                'sftp' => 'Разрешает подключаться по SFTP.',
            ],
        ],

        'plugin' => [
            'description' => 'Даёт возможность управлять плагинами Spigot.',
            'keys' => [
                'read' => 'Разрешает просматривать плагины.',
                'download' => 'Разрешает устанавливать плагины.',
            ],
        ],

        'backup' => [
            'description' => 'Даёт возможность управлять бэкапами.',
            'keys' => [
                'create' => 'Разрешает создавать бэкапы.',
                'read' => 'Разрешает просматривать бэкапы.',
                'delete' => 'Разрешает удалить бэкапы.',
                'download' => 'Разрешает скачивать бэкапы.',
                'restore' => 'Разрешает восстанавливать сервер из бэкапа.',
            ],
        ],

        // Controls permissions for editing or viewing a server's allocations.
        'allocation' => [
            'description' => 'Даёт возможность управлять портами сервера.',
            'keys' => [
                'read' => 'Разрешает просматривать список доступных портов.',
                'create' => 'Разрешает добавлять новые порты.',
                'update' => 'Разрешает пользователю изменять основной порт.',
                'delete' => 'Разрешает пользователю удалять порты.',
            ],
        ],

        // Controls permissions for editing or viewing a server's startup parameters.
        'startup' => [
            'description' => 'Даёт возможность управлять параметрами запуска сервера.',
            'keys' => [
                'read' => 'Разрешает просматривать параметры запуска.',
                'update' => 'Разрешает изменять параметры запуска.',
                'docker-image' => 'Разрешает изменять Docker образ.',
            ],
        ],

        'database' => [
            'description' => 'Даёт возможность управлять базами данных.',
            'keys' => [
                'create' => 'Разрешает создавать новые базы данных.',
                'read' => 'Разрешает просматривать список созданных баз данных.',
                'update' => 'Разрешает обновлять пароль базы данных. Если у него нет разрешения на просмотр пароля - новый пароль он не увидит.',
                'delete' => 'Разрешает удалять базы данных.',
                'view_password' => 'Разрешает смотреть пароль базы данных.',
            ],
        ],

        'schedule' => [
            'description' => 'Даёт возможность управлять планировщиком задач.',
            'keys' => [
                'create' => 'Разрешает создавать новые задачи и действия.', // task.create-schedule
                'read' => 'Разрешает просматривать задачи и действия.', // task.view-schedule, task.list-schedules
                'update' => 'Разрешает изменять имеющиеся задачи и действия.', // task.edit-schedule, task.queue-schedule, task.toggle-schedule
                'delete' => 'Разрешает удалять задачи и действия.', // task.delete-schedule
            ],
        ],

        // TODO: добавить разрешение на удаление, чтобы скрывалась кнопка на фронте

        'settings' => [
            'description' => 'Даёт возможность управлять настройками сервера. Не сможет удалить сервер.',
            'keys' => [
                'rename' => 'Разрешает переименовать сервер и изменить описание.',
                'reinstall' => 'Разрешает переустановить сервер.',
            ],
        ],

        'activity' => [
            'description' => 'Даёт возможность управлять журналом активности.',
            'keys' => [
                'read' => 'Разрешает просматривать журнал активности.',
            ],
        ],
    ];

    /**
     * Returns all the permissions available on the system for a user to
     * have when controlling a server.
     */
    public static function permissions(): Collection
    {
        return Collection::make(self::$permissions);
    }
}
