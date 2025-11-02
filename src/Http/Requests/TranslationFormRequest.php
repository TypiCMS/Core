<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class TranslationFormRequest extends AbstractFormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'key' => ['sometimes', 'required', 'max:255', 'unique:translations,key'],
            'translation.*' => ['nullable', 'max:2000'],
        ];
    }
}
