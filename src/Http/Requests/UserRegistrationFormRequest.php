<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class UserRegistrationFormRequest extends AbstractFormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'locale' => 'nullable|min:2|max:5',
            'my_name' => 'honeypot',
            'my_time' => 'required|honeytime:5',
        ];
    }
}
