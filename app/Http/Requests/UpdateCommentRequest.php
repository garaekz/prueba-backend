<?php

namespace App\Http\Requests;

use Garaekz\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment' => ['required', 'min:3'],
            'likes' => ['integer', 'min:0'],
        ];
    }
}
