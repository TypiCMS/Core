<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class PageSectionFormRequest extends AbstractFormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'page_id' => 'required|integer',
            'position' => 'nullable|integer',
            'image_id' => 'nullable|integer',
            'title.*' => 'nullable|max:255',
            'slug.*' => 'nullable|alpha_dash|max:255|required_if:status.*,1|required_with:title.*',
            'status.*' => 'boolean',
            'body.*' => 'nullable|max:20000',
            'template' => 'nullable|max:255',
        ];
    }
}
