<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
    ];
    protected $casts = [
        'work_date' => 'date',
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
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

    //休憩時間の合計（休憩開始・終了両方とも打刻してあるものを抽出して計算）
    protected function totalBreakMinutes(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->breakTimes
            ->filter(fn($break) => $break->break_start && $break->break_end)
            ->sum(fn($break) => Carbon::parse($break->break_start)->diffInMinutes(Carbon::parse($break->break_end)))
            );
    }

    //勤務時間の合計(出勤〜退勤の総時間-休憩時間の合計＝実働時間)
    protected function totalWorkMinutes(): Attribute
    {
        return Attribute::make(
            get: function(){
        // 出勤・退勤どちらかが未記録なら計算不可
            if (!$this->clock_in || !$this->clock_out) return null;

            return $this->clock_in->diffInMinutes($this->clock_out) - $this->total_break_minutes;
            }
        );
    }
    //勤務時間表示設定
    protected function workTimeDisplay():Attribute
    {
        return Attribute::make(
        get: function () {
            $minutes = $this->total_work_minutes;
            if ($minutes === null) return '--:--';
            return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
        }
    );
    }
    //休憩時間表示設定
    protected function breakTimeDisplay():Attribute
    {
        return Attribute::make(
        get: function () {
            $minutes = $this->total_break_minutes;
            if ($minutes === 0) return '--:--';
            return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
        }
    );
    }

    //ステータス
    protected function status():Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->clock_in) return '勤務外';
                if ($this->clock_out) return '退勤済';

                $onBreak = $this->breakTimes
                    ->first(fn($break) => $break->break_start && !$break->break_end);

                return $onBreak ? '休憩中' : '出勤中';
            }
        );
    }
}