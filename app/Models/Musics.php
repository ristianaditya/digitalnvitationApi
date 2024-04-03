<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musics extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "string";
    protected $table = "musics";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable =[
        'id',
        'music_name',
        'singer',
        'music',
    ];
}
