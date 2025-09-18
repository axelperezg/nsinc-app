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
        Schema::create('estrategies', function (Blueprint $table) {
            $table->id();
            $table->integer('anio');
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->foreignId('juridical_nature_id')->constrained('juridical_natures')->onDelete('cascade');
            $table->text('mision');
            $table->text('vision');
            $table->text('objetivo_institucional');
            $table->text('objetivo_estrategia');
            $table->date('fecha_elaboracion');
            $table->string('estado_estrategia');
            $table->string('concepto');
            $table->date('fecha_envio_dgnc')->nullable();
            $table->decimal('presupuesto', 15, 2)->default(0);
            // Removemos la relación directa con eje_id ya que será muchos a muchos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estrategies');
    }
};
