<?php

namespace App\Models;

use App\Models\Projects;
use App\Models\Transactions;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $KeyType = "int";
    public $timeStamps = true;
    public $incrementing = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'no_phone',
        'enabled',
        'token',
        'gender',
        'role',
        'address',
        'google_id',
        'picture',
        'verified'
    ];
    public function transactions(): HasMany
    {
        return $this->hasMany(Transactions::class, "user_id", "id");
    }
    public function projects(): HasMany
    {
        return $this->hasMany(Projects::class, "user_id", "id");
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function latestTransaction()
    {
        return $this->hasOne(Transactions::class, 'user_id')
            ->whereDate('subscribe_start', '<=', Carbon::now()->setTimezone('Asia/Jakarta'))
            ->whereDate('subscribe_expired', '>=', Carbon::now()->setTimezone('Asia/Jakarta'))
            ->latest('subscribe_start');
    }
}
