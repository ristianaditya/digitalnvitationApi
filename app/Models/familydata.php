<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class familydata extends Model
{
    protected $primaryKey= "id";
    protected $keyType = "int";
    protected $table = "familydatas";
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    protected $fillable =[
        'id',
        'invitation_id',
        'personFirst',
        'genderFirst',
        'personSecond',
        'genderSecond',
        'fatherMale',
        'motherMale',
        'fatherFemale',
        'motherFemale',
    ];
}
