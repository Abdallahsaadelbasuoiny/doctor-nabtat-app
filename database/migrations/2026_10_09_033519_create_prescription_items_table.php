<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('dosage', 100);
            $table->string('frequency', 100);
            $table->string('duration', 100);
            $table->text('instructions')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['prescription_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
