<?php
namespace TypiCMS\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

abstract class AbstractFormRequest extends BaseFormRequest {

    /**
     * Validate the input.
     *
     * @param  \Illuminate\Validation\Factory  $factory
     * @return \Illuminate\Validation\Validator
     */
    public function validator($factory)
    {
        return $factory->make(
            $this->sanitizeInput(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    /**
     * Sanitize the input.
     *
     * @return array
     */
    protected function sanitizeInput()
    {
        if (method_exists($this, 'sanitize'))
        {
            return $this->container->call([$this, 'sanitize']);
        }
        return $this->all();
    }

    /**
     * Authorize
     * 
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }
}
