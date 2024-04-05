<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class guestList extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "guest_lists";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable =[
        'id',
        'invitation_id',
        'name',
        'no_phone',
        'attendance',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitations::class, 'invitation_id');
    }
}
