<?php

namespace Jexactyl\Http\Requests\Admin\Jexactyl;

use Jexactyl\Http\Requests\Admin\AdminFormRequest;

class ServerFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'editing' => 'required|in:true,false',
            'deletion' => 'required|in:true,false',
        ];
    }
}
