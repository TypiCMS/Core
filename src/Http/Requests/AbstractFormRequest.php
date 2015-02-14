<?php
namespace TypiCMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

abstract class AbstractFormRequest extends BaseFormRequest {

    public function authorize()
    {
        return true;
    }
}
