<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->unique(['doctor_id', 'appointment_date'], 'uq_doctor_slot');
        });
    }

    public function down(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
        });

        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropUnique('uq_doctor_slot');
        });

        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->foreign('doctor_id')->references('id')->on('triage_doctors')->cascadeOnDelete();
        });
    }
};
