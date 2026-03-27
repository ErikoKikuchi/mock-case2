<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceChangeRequest extends FormRequest
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
            'clock_in'=>['required'],
            'clock_out'=>['required','after:clock_in'],
            'break_start.*'=>['nullable','after:clock_in','before:clock_out'],
            'break_end.*'=>['nullable','before:clock_out'],
            'reason'=>['required','string']
        ];
    }
    public function messages(){
        return [
            'clock_in.required'=>'出勤時間を入力してください',
            'clock_out.required'=>'退勤時間を入力してください',
            'clock_out.after'=>'出勤時間が不適切な値です',
            'break_start.*.after'=>'休憩時間が不適切な値です',
            'break_start.*.before'=>'休憩時間が不適切な値です',
            'break_end.*.after'=>'休憩時間が不適切な値です',
            'break_end.*.before'=>'休憩時間もしくは退勤時間が不適切な値です',
            'reason.required'=>'備考を記入してください',
            'reason.string'=>'備考を正しく記入してください',
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
