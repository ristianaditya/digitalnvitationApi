<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Projects extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "string";
    protected $table = "projects";
    public $timestamps = true;

    protected $fillable =[
        'id',
        'user_id',
        'editor',
        'html',
        'css',
        'project_name',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(Projects::class, "user_id", "id");
    }
}
