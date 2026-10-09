<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('prescriptions', 'prescription')) {
            Schema::table('prescriptions', function (Blueprint $table): void {
                $table->longText('prescription')->nullable();
            });
        }

        if (Schema::hasTable('prescription_items')) {
            foreach (DB::table('prescriptions')->get() as $prescription) {
                $items = DB::table('prescription_items')
                    ->where('prescription_id', $prescription->id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

                if ($items->isNotEmpty() && blank($prescription->prescription)) {
                    $text = $items->map(fn (object $item): string => trim(implode(' ', array_filter([
                        $item->name,
                        $item->dosage,
                        $item->frequency,
                        $item->duration,
                        $item->instructions,
                    ]))))->implode("\n");

                    DB::table('prescriptions')->where('id', $prescription->id)->update(['prescription' => $text]);
                }
            }

            Schema::drop('prescription_items');
        }
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table): void {
            $table->dropColumn('prescription');
        });
    }
};
