<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'attendance_id',
        'break_id',
        'column_name',
        'before_value',
        'after_value',
    ];

    //リレーション
    public function request()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }

    //参照先
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    public function breakTime()
    {
    return $this->belongsTo(BreakTime::class);
    }
}
