<?php

namespace App\Http\Requests;

use Garaekz\Http\FormRequest;

/**
 * Class StoreUserRequest
 *
 * This class represents the form request for storing a user.
 * It extends the FormRequest class and defines the validation rules for the request data.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fullname' => ['required'],
            'email' => ['required', 'email', 'unique:user,email'],
            'pass' => ['required', 'min:6'],
            'openid' => ['required', 'unique:user,openid'],
        ];
    }
}
