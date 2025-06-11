<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class BlockFormRequest extends AbstractFormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|alpha_dash|unique:blocks,name,' . $this->block?->id,
            'status.*' => 'boolean',
            'body.*' => 'nullable|max:20000',
        ];
    }
}
