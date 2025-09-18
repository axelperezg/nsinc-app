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
        Schema::create('oficio_dgnc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estrategy_id')->constrained()->onDelete('cascade');
            $table->string('numero_oficio');
            $table->date('fecha_oficio');
            $table->string('nombre_archivo');
            $table->text('descripcion_documento')->nullable();
            $table->string('archivo_path'); // Ruta del archivo PDF
            $table->string('archivo_original_name'); // Nombre original del archivo
            $table->string('archivo_mime_type'); // Tipo MIME del archivo
            $table->unsignedBigInteger('archivo_size'); // Tamaño del archivo en bytes
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index(['estrategy_id', 'numero_oficio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficio_dgnc_documents');
    }
};
