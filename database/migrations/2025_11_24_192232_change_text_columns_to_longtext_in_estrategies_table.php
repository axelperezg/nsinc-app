<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiar columnas TEXT a LONGTEXT para permitir contenido más extenso
        DB::statement('ALTER TABLE estrategies MODIFY mision LONGTEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY vision LONGTEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY objetivo_institucional LONGTEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY objetivo_estrategia LONGTEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY entorno_mercado LONGTEXT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY metas_generales LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a TEXT
        DB::statement('ALTER TABLE estrategies MODIFY mision TEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY vision TEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY objetivo_institucional TEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY objetivo_estrategia TEXT NOT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY entorno_mercado TEXT NULL');
        DB::statement('ALTER TABLE estrategies MODIFY metas_generales TEXT NULL');
    }
};
