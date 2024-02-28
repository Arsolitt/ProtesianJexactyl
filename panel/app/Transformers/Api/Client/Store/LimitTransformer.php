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
                'memory' => $model['min'][0],
                'disk' => $model['min'][1],
                'allocations' => $model['min'][2],
                'backups' => $model['min'][3],
                'databases' => $model['min'][4],
            ],
            'max' => [
                'memory' => $model['max'][0],
                'disk' => $model['max'][1],
                'allocations' => $model['max'][2],
                'backups' => $model['max'][3],
                'databases' => $model['max'][4],
            ],
        ];
    }
}
