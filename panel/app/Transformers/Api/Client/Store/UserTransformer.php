<?php

namespace Jexactyl\Transformers\Api\Client\Store;

use Jexactyl\Models\User;
use Jexactyl\Transformers\Api\Client\BaseClientTransformer;

class UserTransformer extends BaseClientTransformer
{

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Return the resource name for the JSONAPI output.
     */
    public function getResourceName(): string
    {
        return User::RESOURCE_NAME;
    }

    /**
     * Transforms a User model into a representation that can be shown to regular
     * users of the API.
     */
    public function transform(User $model): array
    {
        return [
            'balance' => $model->credits,
            'slots' => $model->server_slots,
            'limit' => [
                'cpu' => $this->settings->get('store:limit:cpu', 50),
                'ram' => $this->settings->get('store:limit:ram', 256),
                'disk' => $this->settings->get('store:limit:disk', 1024),
                'ports' => $this->settings->get('store:limit:port', 1),
                'backups' => $this->settings->get('store:limit:backup', 1),
                'databases' => $this->settings->get('store:limit:database', 1),
            ]
        ];
    }
}
