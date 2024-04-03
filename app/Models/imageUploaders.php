<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class imageUploaders extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "image_uploaders";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable =[
        'id',
        'user_id',
        'image',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(imageUploaders::class, "user_id", "id");
    }
}
