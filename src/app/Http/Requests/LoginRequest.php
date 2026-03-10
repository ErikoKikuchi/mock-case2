<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                ],
            'password'=>['required','min:8','string'],
        ];
    }
    public function messages()
    {
        return [
            'email.required'=>'メールアドレスを入力してください',
            'email.string'=>'メールアドレスを正しく入力してください',
            'email.email'=>'メールアドレスはメール形式で入力してください',
            'password.required'=>'パスワードを入力してください',
            'password.min'=>'パスワードは８文字以上で入力してください',
            'password.string'=>'パスワードを正しく入力してください',
        ];
    }
}