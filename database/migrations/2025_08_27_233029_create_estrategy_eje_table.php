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
        // Esta tabla se eliminó posteriormente (2025_11_27) ya que los ejes ahora se almacenan como JSON
        // Solo creamos la tabla si no existe para evitar errores en deployments nuevos
        if (!Schema::hasTable('estrategy_eje')) {
            Schema::create('estrategy_eje', function (Blueprint $table) {
                $table->id();
                $table->foreignId('estrategy_id')->constrained('estrategies')->onDelete('cascade');
                // No creamos foreign key a 'ejes' porque esa tabla no existe
                // La tabla se eliminará en la migración 2025_11_27_182308_drop_unused_pivot_tables
                $table->unsignedBigInteger('eje_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estrategy_eje');
    }
};
