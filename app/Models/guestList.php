<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class guestList extends Model
{
    use SoftDeletes;
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "guest_lists";
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];

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
