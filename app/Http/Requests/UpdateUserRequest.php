<?php

namespace App\Http\Requests;

use Garaekz\Http\FormRequest;

/**
 * Class UpdateUserRequest
 *
 * This class represents the form request for updating a user.
 * It extends the FormRequest class and defines the validation rules for the request data.
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['email', 'unique:user,email'],
            'pass' => ['min:6'],
            'openid' => ['unique:user,openid'],
        ];
    }
}
