<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class RolesFormRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:4|max:255|unique:roles,name,'.$this->id,
        ];
    }
}
