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
            $table->text('programa_sectorial_especial')->nullable()->after('ejes_plan_nacional');
            $table->text('objetivos_estrategicos_transversales')->nullable()->after('programa_sectorial_especial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            $table->dropColumn(['programa_sectorial_especial', 'objetivos_estrategicos_transversales']);
        });
    }
};
