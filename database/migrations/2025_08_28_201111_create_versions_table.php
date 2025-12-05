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
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('fechaInicio');
            $table->date('fechaFinal');
            // Crear la columna sin foreign key primero (la tabla campaigns se crea después)
            // La foreign key se agregará en la migración 2025_12_04_180605_add_foreign_key_campaign_id_to_versions_table
            $table->unsignedBigInteger('campaign_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
