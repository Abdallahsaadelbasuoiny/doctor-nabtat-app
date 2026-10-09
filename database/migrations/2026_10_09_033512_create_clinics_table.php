<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('address', 500)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('subscription_plan')->default('basic');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('fees')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
