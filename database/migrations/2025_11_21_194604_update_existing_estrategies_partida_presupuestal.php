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
        // Actualizar todas las estrategias existentes sin partida_presupuestal
        // asignándoles '36101' (Comunicación Social) por defecto
        DB::table('estrategies')
            ->whereNull('partida_presupuestal')
            ->update(['partida_presupuestal' => '36101']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir - las estrategias deben mantener su partida asignada
    }
};
