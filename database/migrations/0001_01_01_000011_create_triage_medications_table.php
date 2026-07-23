<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triage_medications', function (Blueprint $table) {
            $table->id();
            $table->string('generic_name');
            $table->string('concentration');
            $table->string('form');
            $table->string('route')->nullable();
            $table->string('category')->nullable();
            $table->boolean('controlled')->default(false);
            $table->index('generic_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triage_medications');
    }
};
