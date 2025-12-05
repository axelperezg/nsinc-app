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
        // Esta tabla se eliminó posteriormente (2025_11_27) ya que la relación es directa vía estrategy_id en campaigns
        // Solo creamos la tabla si no existe para evitar errores en deployments nuevos
        if (!Schema::hasTable('estrategy_campaign')) {
            Schema::create('estrategy_campaign', function (Blueprint $table) {
                $table->id();
                $table->foreignId('estrategy_id')->constrained('estrategies')->onDelete('cascade');
                // No creamos foreign key a 'campaigns' porque puede no existir aún o la tabla se eliminará después
                // La tabla se eliminará en la migración 2025_11_27_182308_drop_unused_pivot_tables
                $table->unsignedBigInteger('campaign_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estrategy_campaign');
    }
};
