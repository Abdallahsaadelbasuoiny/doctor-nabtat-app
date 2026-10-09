<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'starts_at',
        'ends_at',
        'status',
        'source',
        'reason',
        'notes',
        'cancellation_reason',
        'checked_in_at',
        'slot_lock',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'slot_lock' => 'integer',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
