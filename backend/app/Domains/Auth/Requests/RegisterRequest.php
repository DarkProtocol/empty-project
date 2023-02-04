<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use App\Data\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
                'unique:users',
            ],
            'nickname' => [
                'bail',
                'required',
                'min:5',
                'max:20',
                'regex:/^[a-zA-Z_0-9]+$/u',
                function ($attribute, $val, $fail) {
                    if ($val[0] === '_' || $val[strlen($val) - 1] === '_') {
                        return $fail(__('validation.regex', ['attribute' => $attribute]));
                    }

                    if (str_contains($val, '__')) {
                        return $fail(__('validation.regex', ['attribute' => $attribute]));
                    }

                    if (preg_match('/\d{5}/ui', $val)) {
                        return $fail(__('validation.regex', ['attribute' => $attribute]));
                    }
                },
                'unique:users',
            ],
            'referrer' => [
                'string',
            ],
            'utm' => [
                'string',
            ],
            'password' => [
                'bail',
                'required',
                Password::min(User::MIN_PASSWORD_LENGTH)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }
}
