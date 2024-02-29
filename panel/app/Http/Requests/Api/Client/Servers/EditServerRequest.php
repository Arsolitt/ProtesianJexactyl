<?php

namespace Jexactyl\Http\Requests\Api\Client\Servers;

use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;
use Jexactyl\Services\Store\LimitsService;

class EditServerRequest extends ClientApiRequest
{
    /**
     * Determine if a client has permission to view this server on the API. This
     * should never be false since this would be checking the same permission as
     * resourceExists().
     */
    public function authorize(): bool
    {
        return true;
    }

    public function __construct(private LimitsService $limitsService)
    {

    }

    /**
     * Rules to validate this request against.
     */
    public function rules(): array
    {
        $limits = $this->limitsService->getLimits();
        return [
            'resource' => 'required|string|in:cpu,memory,disk,allocation_limit,backup_limit,database_limit',
            'amount' => 'required|int',
            'memory' => 'required|numeric|'
        ];
    }
}
