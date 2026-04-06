<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceChangeRequest extends FormRequest
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
            'clock_in'=>['required', 'date_format:H:i'],
            'clock_out'=>['required','after:clock_in', 'date_format:H:i'],
            'break_start.*'=>['nullable','after:clock_in','before:clock_out', 'date_format:H:i'],
            'break_end.*'=>['nullable','before:clock_out', 'date_format:H:i'],
            'reason'=>['required','string','max:255']
        ];
    }
    public function messages(){
        return [
            'clock_in.required'=>'出勤時間を入力してください',
            'clock_in.date_format' => '正しい時間形式で入力してください。',
            'clock_out.required'=>'退勤時間を入力してください',
            'clock_out.after'=>'出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.date_format' => '正しい時間形式で入力してください。',
            'break_start.*.after'=>'休憩時間が不適切な値です',
            'break_start.*.before'=>'休憩時間が不適切な値です',
            'break_start.*.date_format' => '正しい時間形式で入力してください。',
            'break_end.*.before'=>'休憩時間もしくは退勤時間が不適切な値です',
            'break_end.*.date_format' => '正しい時間形式で入力してください。',
            'reason.required'=>'備考を記入してください',
            'reason.string'=>'備考を正しく記入してください',
            'reason.max'=>'理由は２５５文字以内で入力してください',
        ];
    }
    public function withValidator($validator){
        $validator->after(function($validator){
            $breakStarts = $this->break_start ?? [];
            $breakEnds = $this->break_end ?? [];

            foreach($breakStarts as $index => $breakStart){
                $breakEnd = $breakEnds[$index] ?? null;
                if($breakStart && $breakEnd && $breakStart >= $breakEnd){
                    $validator->errors()->add(
                        "break_end.$index",
                        '休憩時間が不適切な値です'
                    );
                }
            }
        });
    }
}