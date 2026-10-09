<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->text('diagnosis');
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['clinic_id', 'doctor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
