<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileFormRequest extends AbstractFormRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'street' => ['nullable', 'max:255'],
            'number' => ['nullable', 'max:255'],
            'box' => ['nullable', 'max:255'],
            'postal_code' => ['nullable', 'max:255'],
            'city' => ['nullable', 'max:255'],
            'country' => ['nullable', 'max:255'],
            'phone' => ['nullable', 'max:100'],
        ];
    }
}
