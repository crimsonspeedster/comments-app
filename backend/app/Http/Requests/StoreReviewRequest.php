<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => [
                'nullable',
                'integer',
                'exists:reviews,id',
            ],
            'user_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'url' => [
                'nullable',
                'url',
                'max:255',
            ],
            'text' => [
                'required',
                'string',
                'max:10000',
            ],
            'attachments' => [
                'sometimes',
                'array',
                'max:5',
            ],
            'attachments.*' => [
                'file',
                'max:102400',
                'mimes:jpg,jpeg,png,gif,txt',
            ],
        ];
    }
}
