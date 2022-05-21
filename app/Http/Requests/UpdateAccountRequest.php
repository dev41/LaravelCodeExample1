<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
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
            'email' => [
                'filled',
                'email',
                Rule::unique('users')->whereNotNull('app_password')->ignore(auth()->id())
            ],
            'first_name' => 'filled',
            'last_name' => 'filled',
            'dob' => 'filled',
            'gender' => 'filled'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'Ack' => 0,
            'msg' => $validator->errors()->first()
        ], 200));
    }

    public function messages()
    {
        return [
            'first_name.filled' => 'First name is required',
            'last_name.filled' => 'Last name is required',
            'dob.filled' => 'Date of birth is required',
        ];
    }
}
