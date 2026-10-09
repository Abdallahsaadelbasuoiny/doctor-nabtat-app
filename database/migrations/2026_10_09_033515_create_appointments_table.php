<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'missed'])->default('pending');
            $table->enum('source', ['online', 'phone', 'in_person'])->default('in_person');
            $table->string('reason', 500)->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('cancellation_reason', 500)->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->unsignedTinyInteger('slot_lock')->default(0);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['clinic_id', 'status', 'starts_at']);
            $table->index(['doctor_id', 'starts_at', 'slot_lock']);
            $table->index(['patient_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
