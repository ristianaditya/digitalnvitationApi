<?php

namespace App\Models;

use App\Models\Musics;
use App\Models\Projects;
use App\Models\familydata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitations extends Model
{
    use SoftDeletes;
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "invitations";
    public $incrementing = true;
    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable =[
        'id',
        'user_id',
        'music_id',
        'project_id',
        'invite_name',
        'invite_link',
        'invite_date',
        'status',
        'clipboard',
    ];

    public function music()
    {
        return $this->belongsTo(Musics::class, 'music_id');
    }

    public function familydata()
    {
        return $this->hasOne(familydata::class, 'invitation_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Projects::class, 'project_id', 'id');
    }
}
