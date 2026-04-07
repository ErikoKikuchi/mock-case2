<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceDetailRequest extends FormRequest
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
            'date'=>['nullable', 'date_format:Y-m-d'],
            'user_id'=>['nullable','integer'],
        ];
    }
    public function messages(): array
    {
        return [
            'date.date_format' => '日付の形式が正しくありません（例: 2025-04-07）',
            'user_id.integer'  => 'ユーザーIDが不正です',
        ];
    }
}