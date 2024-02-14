<?php

namespace Jexactyl\Http\Requests\Admin\Users;

use Jexactyl\Http\Requests\Admin\AdminFormRequest;

class ResourceFormRequest extends AdminFormRequest
{
    /**
     * Rules to apply to requests for updating a users
     * storefront balances via the admin panel.
     */
    public function rules(): array
    {
        return [
            'credits' => 'required|numeric',
            'server_slots' => 'required|int',
        ];
    }
}
