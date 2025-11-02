<?php

namespace TypiCMS\Modules\Core\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class TaxonomyFormRequest extends AbstractFormRequest
{
    /** @return array<string, list<Unique|string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', 'alpha_dash', Rule::unique('taxonomies', 'name')->ignore($this->taxonomy?->id)],
            'title.*' => ['nullable', 'max:255'],
            'slug.*' => ['nullable', 'alpha_dash', 'max:255', 'required_with:title.*'],
            'result_string.*' => ['nullable', 'max:255'],
            'validation_rule' => ['required', 'max:255'],
            'modules' => ['array'],
        ];
    }
}
