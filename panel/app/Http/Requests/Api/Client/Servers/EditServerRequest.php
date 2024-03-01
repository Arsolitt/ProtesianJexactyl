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
            'resources.memory' => 'required|numeric|min:' . $limits['min']['memory'] . '|max:' . $limits['max']['memory'],
            'resources.disk' => 'required|numeric|min:' . $limits['min']['disk'] . '|max:' . $limits['max']['disk'],
            'resources.allocations' => 'required|numeric|min:' . $limits['min']['allocations'] . '|max:' . $limits['max']['allocations'],
            'resources.backups' => 'required|numeric|min:' . $limits['min']['backups'] . '|max:' . $limits['max']['backups'],
            'resources.databases' => 'required|numeric|min:' . $limits['min']['databases'] . '|max:' . $limits['max']['databases'],
        ];
    }
}
