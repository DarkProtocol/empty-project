<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'email' => mb_strtolower($this->input('email')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed[]>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'bail',
                'required',
                'email',
            ],
        ];
    }
}
