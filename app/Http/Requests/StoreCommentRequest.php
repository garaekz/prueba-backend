<?php

namespace App\Http\Requests;

use Garaekz\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment' => ['required'],
            'likes' => ['integer', 'min:0'],
        ];
    }
}
