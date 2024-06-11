<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Projects extends Model
{
    use SoftDeletes;
    protected $primaryKey= "id";
    protected $keyType = "string";
    protected $table = "projects";
    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable =[
        'id',
        'user_id',
        'editor',
        'html',
        'css',
        'project_name',
    ];

    public function user(): HasMany {
        return $this->hasMany(Projects::class, "user_id", "id");
    }
    public function invitation(): HasOne
    {
        return $this->hasOne(Invitations::class, 'project_id', 'id');
    }
}
