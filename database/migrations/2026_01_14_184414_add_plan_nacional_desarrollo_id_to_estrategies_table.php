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
            $table->foreignId('plan_nacional_desarrollo_id')
                  ->nullable()
                  ->after('partida_presupuestal')
                  ->constrained('plan_nacional_desarrollo')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            $table->dropForeign(['plan_nacional_desarrollo_id']);
            $table->dropColumn('plan_nacional_desarrollo_id');
        });
    }
};
