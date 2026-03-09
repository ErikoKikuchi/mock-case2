<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
    ];

    //リレーション
        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function requestItems()
    {
        return $this->hasMany(RequestItem::class);
    }

    //休憩時間の合計
    public function getTotalBreakTimeAttribute()
    {
        return $this->breakTimes
            ->filter(fn($break) => $break->break_start && $break->break_end)//休憩開始・終了両方とも打刻してあるものを抽出
            ->sum(function($break){
                return Carbon::parse($break->break_start)
                    ->diffInMinutes(Carbon::parse($break->break_end));
            });
    }

    //勤務時間の合計
    public function getTotalWorkTimeAttribute()
    {
        // 出勤・退勤どちらかが未記録なら計算不可
        if (!$this->clock_in || !$this->clock_out) return null;

        // 出勤〜退勤の総時間
        $totalTimes = Carbon::parse($this->clock_in)
            ->diffInMinutes(Carbon::parse($this->clock_out));

        // 総時間 - 休憩合計 = 実働時間
        return $totalTimes - $this->total_break_minutes;
    }
    //ステータス
    public function getStatusAttribute()
    {
        // 出勤打刻がない → 勤務外
        if (!$this->clock_in) {
            return '勤務外';
        }

        // 退勤済み → 退勤済
        if ($this->clock_out) {
            return '退勤済';
        }

        // break_startはあるがbreak_endがない = 現在休憩中
        $onBreak = $this->breakTimes
            ->first(fn($break) => $break->break_start && !$break->break_end);

        return $onBreak ? '休憩中' : '出勤中';
    }
}
