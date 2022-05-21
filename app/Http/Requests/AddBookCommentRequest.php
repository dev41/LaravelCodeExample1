<?php

namespace App\Http\Requests;

use App\Rules\WithoutBadWords;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddBookCommentRequest extends FormRequest
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
            'book_id' => 'required',
            'comment' => ['required', new WithoutBadWords()],
            'rating' => 'required|numeric|min:1|max:5',
            'as_femnesty_member' => 'boolean'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'Ack' => 0,
            'msg' => 'Please Provide All Data!!!',
            'bad_language' => array_key_exists('App\Rules\WithoutBadWords', array_collapse($validator->failed()))
        ], 200));
    }
}
