<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
