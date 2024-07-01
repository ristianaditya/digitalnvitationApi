<?php

namespace App\Models;

use App\Models\guestList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class rsvp extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "rsvps";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable =[
        'id',
        'guest_id',
        'greeting',
        'attendance',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(guestList::class, 'guest_id', 'id');
    }
}
