<?php

namespace Jexactyl\Http\Requests\Admin\Jexactyl;

use Jexactyl\Http\Requests\Admin\AdminFormRequest;

class RegistrationFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'registration:enabled' => 'required|in:true,false',
            'registration:verification' => 'required|in:true,false',
            'discord:enabled' => 'required|in:true,false',
            'discord:id' => 'required|int',
            'discord:secret' => 'required|string',
            'registration:slots' => 'required|int',
        ];
    }
}
