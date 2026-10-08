<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->date('appointment_day')->storedAs('date(appointment_date)');
            $table->unique(['user_id', 'appointment_day'], 'uq_patient_day');
        });
    }

    public function down(): void
    {
        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->dropUnique('uq_patient_day');
            $table->dropColumn('appointment_day');
        });

        Schema::table('triage_appointments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
