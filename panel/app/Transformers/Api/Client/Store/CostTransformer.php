<?php

namespace Jexactyl\Transformers\Api\Client\Store;

use Jexactyl\Transformers\Api\Client\BaseClientTransformer;

class CostTransformer extends BaseClientTransformer
{
    /**
     * Return the resource name for the JSONAPI output.
     */
    public function getResourceName(): string
    {
        return 'cost';
    }

    /**
     * Transforms a User model into a representation that can be shown to regular
     * users of the API.
     */
    public function transform(array $model): array
    {
        return [
            'memory' => $model[0],
            'disk' => $model[1],
            'allocations' => $model[2],
            'backups' => $model[3],
            'databases' => $model[4],
            'slots' => $model[5],
        ];
    }
}
