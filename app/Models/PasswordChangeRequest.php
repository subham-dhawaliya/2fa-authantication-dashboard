<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'new_password_hash',
        'otp_code',
        'expires_at',
        'verified',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
