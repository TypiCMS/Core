<?php

namespace TypiCMS\Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class AbstractFormRequest extends FormRequest
{
    /**
     * Authorize.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
