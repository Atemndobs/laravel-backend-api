<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {{name}} extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return {{rules}};
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return {{messages}};
    }
}
