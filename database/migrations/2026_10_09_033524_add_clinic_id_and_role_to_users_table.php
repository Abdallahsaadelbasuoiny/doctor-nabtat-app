<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('clinic_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->enum('role', ['doctor', 'receptionist', 'admin'])->default('receptionist')->after('password');
            $table->string('specialty', 100)->nullable()->after('role');
            $table->boolean('must_change_password')->default(false)->after('specialty');
            $table->boolean('is_active')->default(true)->after('must_change_password');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->foreignId('created_by_user_id')->nullable()->after('last_login_at')->constrained('users')->nullOnDelete();
            $table->index(['clinic_id', 'role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['created_by_user_id']);
            $table->dropForeign(['clinic_id']);
            $table->dropIndex(['clinic_id', 'role', 'is_active']);
            $table->dropColumn([
                'clinic_id',
                'role',
                'specialty',
                'must_change_password',
                'is_active',
                'last_login_at',
                'created_by_user_id',
            ]);
        });
    }
};
