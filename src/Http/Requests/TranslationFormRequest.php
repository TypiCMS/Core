<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class TranslationFormRequest extends AbstractFormRequest
{
    public function rules(): array
    {
        return [
            'key' => 'sometimes|required|max:255|unique:translations,key',
            'translation.*' => 'nullable|max:2000',
        ];
    }
}
