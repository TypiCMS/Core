<?php

namespace TypiCMS\Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class AbstractFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'slug.*.required_if' => __('The slug is required if published.'),
        ];
    }

    public function attributes()
    {
        return [
            'title.*' => Str::lower(__('Title')),
            'slug.*' => Str::lower(__('Slug')),
            'status.*' => Str::lower(__('Published')),
            'summary.*' => Str::lower(__('Summary')),
            'body.*' => Str::lower(__('Body')),
            'website.*' => Str::lower(__('Website')),
            'url.*' => Str::lower(__('Url')),
            'meta_keywords.*' => Str::lower(__('Meta keywords')),
            'meta_description.*' => Str::lower(__('Meta description')),
        ];
    }
}
