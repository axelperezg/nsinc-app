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
        // Eliminar tabla pivot estrategy_campaign (relación es directa vía estrategy_id en campaigns)
        Schema::dropIfExists('estrategy_campaign');

        // Eliminar tabla pivot estrategy_eje (ejes se almacenan en campo JSON ejes_plan_nacional)
        Schema::dropIfExists('estrategy_eje');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear estrategy_campaign
        Schema::create('estrategy_campaign', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estrategy_id')->constrained('estrategies')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->timestamps();
        });

        // Recrear estrategy_eje
        Schema::create('estrategy_eje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estrategy_id')->constrained('estrategies')->onDelete('cascade');
            $table->foreignId('eje_id')->constrained('ejes')->onDelete('cascade');
            $table->timestamps();
        });
    }
};
