<?php

namespace Jexactyl\Services\Store;

class LimitsService
{
    public function getLimits(): array
    {
        $data = [];
        $prefixMin = 'store:limit:min:';
        $prefixMax = 'store:limit:max:';
        $types = ['memory', 'disk', 'allocations', 'backups', 'databases'];

        foreach ($types as $type) {
            $data['min'][$type] = settings()->get($prefixMin . $type, 0);
            $data['max'][$type] = settings()->get($prefixMax . $type, 0);
        }

        return $data;
    }

}
