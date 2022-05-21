<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateHubRequest extends FormRequest
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
            'title' => 'filled',
            'category_id' => 'filled',
            'organizer' => 'filled',
            'postal_code' => 'filled',
            'email' => 'filled|email',
            'phone' => 'filled',
            'description' => 'filled',
            'type' => 'filled',
            'privacy' => 'filled',
            'start_date' => 'filled',
            'end_date' => 'filled'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'Ack' => 0,
            'msg' => $validator->errors()->first() ?: 'Please Provide Correct Data!'
        ], 200));
    }
}
