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
            $table->enum('partida_presupuestal', ['36101', '36201'])
                  ->default('36101')
                  ->after('institution_id')
                  ->comment('Partida presupuestal: 36101=Comunicación Social, 36201=Promoción y Publicidad');

            $table->text('entorno_mercado')
                  ->nullable()
                  ->after('objetivo_estrategia')
                  ->comment('Campo específico para partida 36201 (Promoción y Publicidad)');

            $table->text('metas_generales')
                  ->nullable()
                  ->after('entorno_mercado')
                  ->comment('Campo específico para partida 36201 (Promoción y Publicidad)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            $table->dropColumn(['partida_presupuestal', 'entorno_mercado', 'metas_generales']);
        });
    }
};
