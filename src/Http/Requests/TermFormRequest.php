<?php

declare(strict_types=1);

namespace TypiCMS\Modules\Core\Http\Requests;

class TermFormRequest extends AbstractFormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'title.*' => ['nullable', 'max:255'],
            'slug.*' => ['nullable', 'alpha_dash', 'max:255', 'required_with:title.*'],
        ];
    }
}
