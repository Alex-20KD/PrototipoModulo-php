<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->text('anamnesis')->nullable();
            $table->text('antecedentes')->nullable();
            $table->string('cie10_code', 10)->nullable();
            $table->string('cie10_description')->nullable();
            $table->enum('diagnosis_type', ['presuntivo', 'definitivo'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropColumn(['anamnesis', 'antecedentes', 'cie10_code', 'cie10_description', 'diagnosis_type']);
        });
    }
};
