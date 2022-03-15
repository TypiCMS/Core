<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class PageFormRequest extends AbstractFormRequest
{
    public function rules()
    {
        $rules = [
            'image_id' => 'nullable|integer',
            'module' => 'nullable|max:255',
            'template' => 'nullable|max:255',
            'title.*' => 'nullable|max:255',
            'uri.*' => 'nullable',
            'status.*' => 'nullable',
            'body.*' => 'nullable',
            'meta_keywords.*' => 'nullable|max:255',
            'meta_description.*' => 'nullable|max:255',
            'position' => 'integer',
            'parent_id' => 'nullable|integer',
            'is_home' => 'boolean',
            'private' => 'boolean',
            'redirect' => 'boolean',
            'css' => 'nullable',
            'js' => 'nullable',
        ];

        if ($this->is_home) {
            $rules['slug.*'] = 'nullable|alpha_dash|max:255';
        } else {
            $rules['slug.*'] = 'nullable|alpha_dash|max:255|exclude_if:is_home,1|required_with:title.*';
        }

        return $rules;
    }
}
