<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $table='requests';

    protected $fillable = [
        'user_id',
        'attendance_id',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'corrected_by',
        'requested_by',
    ];

    protected $casts = ['approved_at' => 'datetime'];

//リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestItems()
    {
        return $this->hasMany(RequestItem::class,'request_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

//承認者
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

//ステータスラベル
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match($this->status) {
                'pending'  => '申請中',
                'approved' => '承認済',
                default    => '不明',
            }
        );
    }
//詳細画面でAttendanceRequestの取得用
    public static function latestByAttendance(Attendance $attendance): ?AttendanceRequest
    {
        return static::whereHas('requestItems', function($query) use ($attendance) {
            $query->where('attendance_id', $attendance->id);
        })->latest()->first();
    }
//申請後の詳細画面の表示用：clock_in
    public function getClockInValueAttribute(): ?string
    {
        $value = $this->requestItems->firstWhere('column_name', 'clock_in')?->after_value;
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }
//申請後の詳細画面の表示用：clock_out
    public function getClockOutValueAttribute(): ?string
    {
        $value = $this->requestItems->firstWhere('column_name', 'clock_out')?->after_value;
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }
//申請後の詳細画面の表示用：break
    public function getBreakItemsAttribute()
    {
        return $this->requestItems
            ->where('column_name', 'break_start')
            ->map(function($startItem) {
            $breakEnd = $this->requestItems
                ->filter(fn($item) => $item->column_name === 'break_end'
                    && $item->break_id === $startItem->break_id)
                ->first()?->after_value;

            return [
                'break_start' => Carbon::parse($startItem->after_value)->format('H:i'),
                'break_end' => $breakEnd ? Carbon::parse($breakEnd)->format('H:i') : null,
            ];
        });
    }

}
