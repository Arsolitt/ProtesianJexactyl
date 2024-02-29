<?php

namespace Jexactyl\Services\Store;

class LimitsService
{
    public function getLimits(): array
    {
        $data = [];
        $prefixMin = 'store:limit:min:';
        $prefixMax = 'store:limit:max:';
        $types = ['memory', 'disk', 'allocation', 'backup', 'database'];

        foreach ($types as $type) {
            $data['min'][] = settings()->get($prefixMin . $type, 0);
            $data['max'][] = settings()->get($prefixMax . $type, 0);
        }

        return $data;
    }

}
