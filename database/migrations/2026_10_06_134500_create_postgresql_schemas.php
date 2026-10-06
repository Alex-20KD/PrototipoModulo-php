<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE SCHEMA IF NOT EXISTS security;');
            DB::statement('CREATE SCHEMA IF NOT EXISTS clinical;');
            DB::statement('CREATE SCHEMA IF NOT EXISTS agenda;');
            DB::statement('CREATE SCHEMA IF NOT EXISTS catalogs;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP SCHEMA IF EXISTS security CASCADE;');
            DB::statement('DROP SCHEMA IF EXISTS clinical CASCADE;');
            DB::statement('DROP SCHEMA IF EXISTS agenda CASCADE;');
            DB::statement('DROP SCHEMA IF EXISTS catalogs CASCADE;');
        }
    }
};
