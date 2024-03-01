<?php

namespace Jexactyl\Transformers\Api\Client\Store;

use Jexactyl\Transformers\Api\Client\BaseClientTransformer;

class LimitTransformer extends BaseClientTransformer
{
    /**
     * Return the resource name for the JSONAPI output.
     */
    public function getResourceName(): string
    {
        return 'limit';
    }

    /**
     * Transforms a User model into a representation that can be shown to regular
     * users of the API.
     */
    public function transform(array $model): array
    {
        return [
            'min' => [
                'memory' => $model['min']['memory'],
                'disk' => $model['min']['disk'],
                'allocations' => $model['min']['allocations'],
                'backups' => $model['min']['backups'],
                'databases' => $model['min']['databases'],
            ],
            'max' => [
                'memory' => $model['max']['memory'],
                'disk' => $model['max']['disk'],
                'allocations' => $model['max']['allocations'],
                'backups' => $model['max']['backups'],
                'databases' => $model['max']['databases'],
            ],
        ];
    }
}
