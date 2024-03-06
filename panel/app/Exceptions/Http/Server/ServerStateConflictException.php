<?php

namespace Jexactyl\Exceptions\Http\Server;

use Jexactyl\Models\Server;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ServerStateConflictException extends ConflictHttpException
{
    /**
     * Exception thrown when the server is in an unsupported state for API access or
     * certain operations within the codebase.
     */
    public function __construct(Server $server, \Throwable $previous = null)
    {
        $message = 'This server is currently in an unsupported state, please try again later.';
        if ($server->isSuspended()) {
            $message = 'Сервер заморожен за неуплату!';
        } elseif ($server->node->isUnderMaintenance()) {
            $message = 'Узел, на котором находится сервер в режиме технического обслуживания!';
        } elseif (!$server->isInstalled()) {
            $message = 'Сервер в процессе установки!';
        } elseif ($server->status === Server::STATUS_RESTORING_BACKUP) {
            $message = 'Сервер восстанавливается из бэкапа!';
        } elseif (!is_null($server->transfer)) {
            $message = 'Сервер в процессе переноса на другой узел!';
        }

        parent::__construct($message, $previous);
    }
}
