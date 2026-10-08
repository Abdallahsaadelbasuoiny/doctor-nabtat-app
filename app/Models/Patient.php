<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'name',
        'date_of_birth',
        'gender',
        'address',
        'phone_encrypted',
        'phone_bindex',
        'national_id_encrypted',
        'national_id_bindex',
        'created_by_user_id',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}