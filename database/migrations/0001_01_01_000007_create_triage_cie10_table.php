<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triage_cie10', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('description');
            $table->index('description');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triage_cie10');
    }
};
