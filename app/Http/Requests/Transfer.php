<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Transfer extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            
            'phone'=>'required',
            'amount'=>'required | integer'
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => 'Please fill phone number',
            'amount.required' => 'Please fill Amount'
        ];
    }
}
