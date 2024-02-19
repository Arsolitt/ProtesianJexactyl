<?php

namespace Jexactyl\Http\Requests\Api\Client\Store;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Request;
use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;

class PaymentRequest extends ClientApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $methods = ['Yookassa', 'Lava'];
        $min = settings()->get('gateway:' . Request::input('method') . ':min', 1);
        $max = settings()->get('gateway:' . Request::input('method') . ':max', 9999);
        return [
            'method' => 'required|string|in:' . implode(',', $methods),
            'amount' => 'required|int|min:' . $min . '|max:' . $max,
        ];
    }
}
