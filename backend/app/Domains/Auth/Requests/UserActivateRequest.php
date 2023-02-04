<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserActivateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed[]>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'size:64'
            ],
        ];
    }
}
