<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triage_doctors', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('especialidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triage_doctors');
    }
};
