<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 * @package App\Http\Requests\Auth
 *
 * @property string email
 * @property string password
 * @property string phone
 * @property string code
 */
class LoginRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required_without:phone|nullable|email|max:255',
            'password' => 'required_with:email|nullable|min:6|max:255',
            'phone'    => 'required_without:email|nullable|regex:/\+[0-9]{10,15}/',
            'code'     => 'required_with:phone|nullable|digits:6|otp'
        ];
    }

    public function credentials()
    {
        return null !== $this->email
            ? $this->emailCredentials()
            : $this->phoneCredentials();
    }

    private function emailCredentials()
    {
        return [
            'email'    => $this->email,
            'password' => $this->password
        ];
    }

    private function phoneCredentials()
    {
        return [
            'phone' => $this->phone,
            'code'  => $this->code
        ];
    }
}
