<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class UserRegistrationFormRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email:rfc,dns|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'password' => 'required|min:8|max:255|confirmed',
            'my_name' => 'honeypot',
            'my_time' => 'required|honeytime:5',
        ];
    }
}
