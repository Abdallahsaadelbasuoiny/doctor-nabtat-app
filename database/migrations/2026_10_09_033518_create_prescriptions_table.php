<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('issued_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['doctor_id', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
