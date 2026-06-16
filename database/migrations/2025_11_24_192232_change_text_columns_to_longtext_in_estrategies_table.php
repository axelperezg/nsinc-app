<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiar columnas TEXT a LONGTEXT para permitir contenido más extenso
        Schema::table('estrategies', function (Blueprint $table) {
            $table->longText('mision')->nullable(false)->change();
            $table->longText('vision')->nullable(false)->change();
            $table->longText('objetivo_institucional')->nullable(false)->change();
            $table->longText('objetivo_estrategia')->nullable(false)->change();
            $table->longText('entorno_mercado')->nullable()->change();
            $table->longText('metas_generales')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a TEXT
        Schema::table('estrategies', function (Blueprint $table) {
            $table->text('mision')->nullable(false)->change();
            $table->text('vision')->nullable(false)->change();
            $table->text('objetivo_institucional')->nullable(false)->change();
            $table->text('objetivo_estrategia')->nullable(false)->change();
            $table->text('entorno_mercado')->nullable()->change();
            $table->text('metas_generales')->nullable()->change();
        });
    }
};
