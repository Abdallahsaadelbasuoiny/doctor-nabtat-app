<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card'])->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
            $table->index(['clinic_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
