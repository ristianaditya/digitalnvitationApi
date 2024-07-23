<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class kuesioner extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "kuesioners";
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    protected $fillable =[
        'id',
        'user_id',
        'quiz',
        'feedback',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
