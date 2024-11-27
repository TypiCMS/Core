<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class BlockFormRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:255|alpha_dash|unique:blocks,name,' . $this->block?->id,
            'status.*' => 'boolean',
            'body.*' => 'nullable|max:10000',
        ];
    }
}
