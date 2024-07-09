<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class assetsEditor extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "assets_editors";
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    protected $fillable =[
        'id',
        'user_id',
        'file_name',
        'file_path',
        'type',
    ];
}
