<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Requests;

class UserRegistrationFormRequest extends AbstractFormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'locale' => ['nullable', 'min:2', 'max:5'],
            'my_name' => ['honeypot'],
            'my_time' => ['required', 'honeytime:5'],
        ];
    }
}
