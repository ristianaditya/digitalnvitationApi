<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'otp', 'expires_at', 'attempts', 'last_attempt_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_attempt_at' => 'datetime'
    ];
}
