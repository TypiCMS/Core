<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class TranslationFormRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'key' => 'required|max:255|unique:translations,key,'.$this->id,
            'translation.*' => 'nullable',
        ];
    }
}
