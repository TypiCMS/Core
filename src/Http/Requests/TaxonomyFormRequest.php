<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class TaxonomyFormRequest extends AbstractFormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:255|alpha_dash|unique:taxonomies,name,'.$this->id,
            'title.*' => 'nullable|max:255',
            'slug.*' => 'nullable|alpha_dash|max:255|required_with:title.*',
            'result_string.*' => 'nullable|max:255',
            'validation_rule' => 'nullable|max:255',
            'modules' => 'array',
        ];
    }
}
