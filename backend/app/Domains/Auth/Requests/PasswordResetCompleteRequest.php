<?php

declare(strict_types=1);

namespace App\Domains\Auth\Requests;

use App\Data\Models\PasswordReset;
use App\Data\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordResetCompleteRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed[]>
     */
    public function rules(): array
    {
        return [
            'token' => [
                'bail',
                'required',
                'size:' . PasswordReset::TOKEN_LENGTH,
            ],
            'password' => [
                'bail',
                'required',
                Password::min(User::MIN_PASSWORD_LENGTH)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }
}
