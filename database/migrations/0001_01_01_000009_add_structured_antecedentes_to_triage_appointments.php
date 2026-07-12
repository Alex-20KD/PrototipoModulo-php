<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            // Hipertensión
            $table->boolean('ant_hta')->default(false);
            $table->integer('ant_hta_years')->nullable();
            $table->boolean('ant_hta_treatment')->default(false);
            $table->string('ant_hta_medication')->nullable();

            // Diabetes
            $table->boolean('ant_dm')->default(false);
            $table->integer('ant_dm_years')->nullable();
            $table->boolean('ant_dm_treatment')->default(false);
            $table->string('ant_dm_medication')->nullable();

            // Enfermedades crónicas (checkboxes, stored as JSON)
            $table->json('ant_chronic')->nullable();

            // Si marcó 'otra', descripción libre
            $table->string('ant_chronic_other')->nullable();

            // Observaciones adicionales
            $table->text('ant_observations')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'ant_hta', 'ant_hta_years', 'ant_hta_treatment', 'ant_hta_medication',
                'ant_dm', 'ant_dm_years', 'ant_dm_treatment', 'ant_dm_medication',
                'ant_chronic', 'ant_chronic_other', 'ant_observations',
            ]);
        });
    }
};
