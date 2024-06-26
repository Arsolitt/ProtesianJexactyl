<?php

namespace Jexactyl\Http\Requests\Admin\Jexactyl;

use Jexactyl\Http\Requests\Admin\AdminFormRequest;

class StoreFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'store:enabled' => 'required|in:true,false',

            'store:yookassa:enabled' => 'required|in:true,false',
            'store:yookassa:name' => 'required|string|min:1|max:15',
            'store:yookassa:min' => 'required|int|min:1',
            'store:yookassa:max' => 'required|int|min:9999',

            'store:currency' => 'required|min:1|max:10',

            'store:cost:memory' => 'required|int|min:1',
            'store:cost:disk' => 'required|int|min:1',
            'store:cost:slot' => 'required|int|min:1',
            'store:cost:allocation' => 'required|int|min:1',
            'store:cost:backup' => 'required|int|min:1',
            'store:cost:database' => 'required|int|min:1',

            'store:limit:min:memory' => 'required|int|min:256',
            'store:limit:min:disk' => 'required|int|min:1024',
            'store:limit:min:allocations' => 'required|int|min:1',
            'store:limit:min:backups' => 'required|int|min:0',
            'store:limit:min:databases' => 'required|int|min:0',

            'store:limit:max:memory' => 'required|int|min:256',
            'store:limit:max:disk' => 'required|int|min:1024',
            'store:limit:max:allocations' => 'required|int|min:1',
            'store:limit:max:backups' => 'required|int|min:0',
            'store:limit:max:databases' => 'required|int|min:0',
        ];
    }
}
