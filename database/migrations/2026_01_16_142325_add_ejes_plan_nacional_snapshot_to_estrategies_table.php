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
            // Snapshot de los ejes seleccionados con su texto al momento de guardar
            // Estructura: [{"key": "eje1", "label": "Texto del eje", "description": "...", "tipo": "general|transversal"}]
            $table->json('ejes_plan_nacional_snapshot')->nullable()->after('ejes_plan_nacional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            $table->dropColumn('ejes_plan_nacional_snapshot');
        });
    }
};
