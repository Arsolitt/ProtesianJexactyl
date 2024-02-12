<?php

namespace Jexactyl\Http\Requests\Admin\Jexactyl;

use Jexactyl\Http\Requests\Admin\AdminFormRequest;

class StoreFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'store:enabled' => 'required|in:true,false',
            'store:paypal:enabled' => 'required|in:true,false',
            'store:stripe:enabled' => 'required|in:true,false',
            'store:yookassa:enabled' => 'required|in:true,false',
            'store:lava:enabled' => 'required|in:true,false',
            'store:currency' => 'required|min:1|max:10',

            'store:cost:cpu' => 'required|int|min:1',
            'store:cost:memory' => 'required|int|min:1',
            'store:cost:disk' => 'required|int|min:1',
            'store:cost:slot' => 'required|int|min:1',
            'store:cost:port' => 'required|int|min:1',
            'store:cost:backup' => 'required|int|min:1',
            'store:cost:database' => 'required|int|min:1',

            'store:limit:cpu' => 'required|int|min:50',
            'store:limit:memory' => 'required|int|min:256',
            'store:limit:disk' => 'required|int|min:1024',
            'store:limit:port' => 'required|int|min:1',
            'store:limit:backup' => 'required|int|min:1',
            'store:limit:database' => 'required|int|min:1',
        ];
    }
}
