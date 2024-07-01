<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "transactions";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable =[
        'user_id',
        'amount',
        'status',
        'subscribe',
        'id_payment',
        'subscribe_start',
        'subscribe_expired'
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id", "id");
    }
}


