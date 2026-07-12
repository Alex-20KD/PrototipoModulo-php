<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triage_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('triage_appointments');
            $table->string('generic_name');
            $table->string('concentration');
            $table->string('form');
            $table->integer('quantity');
            $table->text('indications');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triage_prescriptions');
    }
};
