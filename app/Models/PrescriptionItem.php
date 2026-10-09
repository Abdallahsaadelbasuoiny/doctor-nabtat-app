<?php

namespace App\Models;

use Database\Factories\PrescriptionItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    /** @use HasFactory<PrescriptionItemFactory> */
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'name',
        'dosage',
        'frequency',
        'duration',
        'instructions',
        'sort_order',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }
}
