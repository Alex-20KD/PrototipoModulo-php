<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new column alongside old one
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->string('diagnosis_type_new', 30)->nullable()
                  ->after('diagnosis_type');
        });

        // Migrate existing data
        DB::table('triage_appointments')->whereNotNull('diagnosis_type')
          ->update(['diagnosis_type_new' => DB::raw("
            CASE diagnosis_type
              WHEN 'presuntivo' THEN 'presuntivo_ingreso'
              WHEN 'definitivo' THEN 'definitivo_ingreso'
              ELSE NULL
            END
          ")]);

        // Drop old column and rename new one
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropColumn('diagnosis_type');
        });
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->renameColumn('diagnosis_type_new', 'diagnosis_type');
        });
    }

    public function down(): void
    {
        // Add old column type
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->string('diagnosis_type_old', 20)->nullable()
                  ->after('diagnosis_type');
        });

        // Migrate existing data back
        DB::table('triage_appointments')->whereNotNull('diagnosis_type')
          ->update(['diagnosis_type_old' => DB::raw("
            CASE diagnosis_type
              WHEN 'presuntivo_ingreso' THEN 'presuntivo'
              WHEN 'definitivo_ingreso' THEN 'definitivo'
              WHEN 'presuntivo_alta' THEN 'presuntivo'
              WHEN 'definitivo_alta' THEN 'definitivo'
              ELSE NULL
            END
          ")]);

        // Drop new column and rename old one
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropColumn('diagnosis_type');
        });
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->renameColumn('diagnosis_type_old', 'diagnosis_type');
        });
    }
};
