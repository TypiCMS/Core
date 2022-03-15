<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class FormRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:255|alpha_dash|unique:blocks,name,'.$this->id,
            'status.*' => 'boolean',
            'body.*' => 'nullable',
        ];
    }
}
