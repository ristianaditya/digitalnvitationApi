<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitations extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "invitations";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable =[
        'id',
        'user_id',
        'music_id',
        'project_id',
        'invite_link',
        'invite_date',
    ];

    public function music()
    {
        return $this->belongsTo(Musics::class, 'music_id');
    }
}
