<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateMessageRequest extends FormRequest
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
            'files.*' => 'mimes:jpg,jpeg,gif,png'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'Ack' => 0,
            'msg' => 'Please Provide All Data!'
        ], 200));
    }

    public function validate()
    {
        parent::validate();

        $instance = $this->getValidatorInstance();
        $data = $this->all();

        if (
            (!isset($data['text']) || (isset($data['text']) && strlen(trim($data['text'])) <= 0))
            && (!isset($data['files']) || (isset($data['files']) && empty($data['files'])))
            && (!isset($data['sharing_data']) || (isset($data['sharing_data']) && empty($data['sharing_data'])))
        ) {
            $this->failedValidation($instance);
        }
    }
}
