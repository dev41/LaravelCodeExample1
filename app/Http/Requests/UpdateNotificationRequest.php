<?php

namespace App\Http\Requests;

use App\Models\Notification;

class UpdateNotificationRequest extends FormRequest
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
            'is_view' => 'numeric|in:' . Notification::IS_NOT_VIEWED . ',' . Notification::IS_VIEWED
        ];
    }
}
