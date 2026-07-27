<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('triage_vital_signs', function (Blueprint $table) {
            $table->unsignedSmallInteger('respiratory_rate')->nullable()->after('heart_rate');
        });
    }

    public function down(): void
    {
        Schema::table('triage_vital_signs', function (Blueprint $table) {
            $table->dropColumn('respiratory_rate');
        });
    }
};
