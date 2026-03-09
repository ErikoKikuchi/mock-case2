<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $table='requests';

    protected $fillable = [
        'user_id',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];
//リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestItems()
    {
        return $this->hasMany(RequestItem::class);
    }

//承認者
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

//申請中・承認済
public function getIsPendingAttribute(): bool
{
    return $this->status === 'pending';
}

public function getIsApprovedAttribute(): bool
{
    return $this->status === 'approved';
}
}
