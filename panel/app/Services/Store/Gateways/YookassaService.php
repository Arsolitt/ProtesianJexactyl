<?php

namespace Jexactyl\Services\Store\Gateways;

class YookassaService
{
    public function handle(array $data): array
    {
        // TODO: implement Yookassa logic and return updated data
        $data['url'] = 'testUrl';
        $data['external_id'] = 'testExternalId';
        return $data;
    }
}
