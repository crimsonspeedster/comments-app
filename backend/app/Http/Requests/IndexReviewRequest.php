<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:25',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'sort_by' => [
                'sometimes',
                Rule::in([
                    'user_name',
                    'email',
                    'created_at',
                ]),
            ],
            'sort_direction' => [
                'sometimes',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ];
    }
}
