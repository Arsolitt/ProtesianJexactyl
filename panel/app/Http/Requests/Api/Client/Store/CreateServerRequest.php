<?php

namespace Jexactyl\Http\Requests\Api\Client\Store;

use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;

class CreateServerRequest extends ClientApiRequest
{
    /**
     * Rules to validate this request against.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:191',
            'description' => 'nullable|string|min:3|max:191',
            'memory' => 'required|numeric|min:1',
            'disk' => 'required|numeric|min:1',
            'allocations' => 'required|int|min:1',
            'backups' => 'nullable|int',
            'databases' => 'nullable|int',
            'egg' => 'required|int|min:1',
            'nest' => 'required|int|min:1',
            'project_id' => 'sometimes|nullable|string|min:1|max:255',
            'version_id' => 'sometimes|nullable|string|min:1|max:255',
        ];
    }
}
