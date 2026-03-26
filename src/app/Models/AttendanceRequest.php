<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
}
