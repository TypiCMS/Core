<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class AbstractFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'slug.*.required_if' => __('The slug is required if published.'),
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'title.*' => Str::lower(__('Title')),
            'slug.*' => Str::lower(__('Slug')),
            'status.*' => Str::lower(__('Published')),
            'summary.*' => Str::lower(__('Summary')),
            'body.*' => Str::lower(__('Body')),
            'website.*' => Str::lower(__('Website')),
            'url.*' => Str::lower(__('Url')),
            'meta_title.*' => Str::lower(__('Meta title')),
            'meta_description.*' => Str::lower(__('Meta description')),
            'meta_keywords.*' => Str::lower(__('Meta keywords')),
        ];
    }
}
