<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class MenuFormRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'image_id' => 'nullable|integer',
            'name' => 'required|max:255|alpha_dash|unique:menus,name,'.$this->id,
            'class' => 'nullable|max:255',
            'status.*' => 'boolean',
        ];
    }
}
