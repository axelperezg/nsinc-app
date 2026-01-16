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
        Schema::create('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->integer('periodo_inicio');
            $table->integer('periodo_fin');
            $table->boolean('activo')->default(false);
            $table->string('nombre_ejes_generales', 255)->default('Ejes Generales');
            $table->string('nombre_ejes_transversales', 255)->default('Ejes Transversales');
            $table->json('ejes_generales');
            $table->json('ejes_transversales');
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para optimización
            $table->index('activo');
            $table->index(['periodo_inicio', 'periodo_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_nacional_desarrollo');
    }
};
