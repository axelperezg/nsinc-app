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
        Schema::table('estrategies', function (Blueprint $table) {
            // Agregar campo concepto si no existe
            if (!Schema::hasColumn('estrategies', 'concepto')) {
                $table->string('concepto')->default('Registro');
            }
            
            // Agregar campo estrategia_original_id para relacionar modificaciones/solventaciones
            if (!Schema::hasColumn('estrategies', 'estrategia_original_id')) {
                $table->foreignId('estrategia_original_id')->nullable()->constrained('estrategies')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            $table->dropForeign(['estrategia_original_id']);
            $table->dropColumn(['concepto', 'estrategia_original_id']);
        });
    }
};
