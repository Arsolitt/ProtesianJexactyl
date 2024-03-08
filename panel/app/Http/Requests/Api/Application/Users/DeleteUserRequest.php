<?php

namespace Jexactyl\Http\Requests\Api\Application\Users;

use Jexactyl\Services\Acl\Api\AdminAcl;
use Jexactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class DeleteUserRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_USERS;

    protected int $permission = AdminAcl::WRITE;
}
