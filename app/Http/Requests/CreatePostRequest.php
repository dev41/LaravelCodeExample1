<?php

namespace App\Http\Requests;

use App\Rules\WithoutBadWords;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePostRequest extends FormRequest
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
            'images.*' => 'mimes:jpeg,gif,png',
            'description' => [new WithoutBadWords()]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'Ack' => 0,
            'msg' => $validator->errors()->first() ?: 'Please Provide All Data!',
            'bad_language' => array_key_exists('App\Rules\WithoutBadWords', array_collapse($validator->failed()))
        ], 200));
    }

    public function validate()
    {
        parent::validate();

        $instance = $this->getValidatorInstance();
        $data = $this->all();

        if (isset($data['sharing_data'])) {
            $data['sharing_data'] = str_replace('{}', '', $data['sharing_data']);
        }

        if (
            (!isset($data['sharing_data']) || (isset($data['sharing_data']) && strlen(trim($data['sharing_data'])) <= 0))
            && (!isset($data['description']) || (isset($data['description']) && strlen(trim($data['description'])) <= 0))
            && (!isset($data['images']) || (isset($data['images']) && empty($data['images'])))
        ) {
            $this->failedValidation($instance);
        }
    }
}
