<?php

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'amount',
        'discount',
        'payment_method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount' => 'decimal:2',
        ];
    }

    public function getNetTotalAttribute(): string
    {
        $netTotalInCents = $this->amountInCents() - (int) round((float) $this->discount * 100);

        return sprintf('%d.%02d', intdiv($netTotalInCents, 100), $netTotalInCents % 100);
    }

    public function amountInCents(): int
    {
        return (int) round((float) $this->amount * 100);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
