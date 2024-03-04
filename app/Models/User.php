<?php

namespace App\Models;

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
    protected $fillable =[
        'username',
        'password',
        'name',
        'email',
        'no_phone',
        'token',
        'gender',
        'role',
        'subscribe',
        'address',
        'picture'
    ];
    public function transactions(): HasMany
    {
        return $this->hasMany(Transactions::class, "user_id", "id");
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
