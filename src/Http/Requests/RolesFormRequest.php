<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class RolesFormRequest extends AbstractFormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'name' => 'required|min:4|max:255|unique:roles,name,' . $this->role?->id,
        ];
    }
}
