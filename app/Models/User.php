<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // أضف HasApiTokens هنا

    protected $fillable = [
        'clinic_id',
        'name',
        'phone',
        'email',
        'password',
        'role',
        'must_change_password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}