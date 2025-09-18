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
        Schema::table('campaigns', function (Blueprint $table) {
            // Cambiar la precisión de todas las columnas de presupuesto
            // de decimal(10, 6) a decimal(15, 2) para permitir valores más grandes
            
            $table->decimal('televisoras', 15, 2)->default(0)->nullable()->change();
            $table->decimal('radiodifusoras', 15, 2)->default(0)->nullable()->change();
            $table->decimal('cine', 15, 2)->default(0)->nullable()->change();
            $table->decimal('decdmx', 15, 2)->default(0)->nullable()->change();
            $table->decimal('deedos', 15, 2)->default(0)->nullable()->change();
            $table->decimal('deextr', 15, 2)->default(0)->nullable()->change();
            $table->decimal('revistas', 15, 2)->default(0)->nullable()->change();
            $table->decimal('mediosComplementarios', 15, 2)->default(0)->nullable()->change();
            $table->decimal('mediosDigitales', 15, 2)->default(0)->nullable()->change();
            $table->decimal('preEstudios', 15, 2)->default(0)->nullable()->change();
            $table->decimal('postEstudios', 15, 2)->default(0)->nullable()->change();
            $table->decimal('disenio', 15, 2)->default(0)->nullable()->change();
            $table->decimal('produccion', 15, 2)->default(0)->nullable()->change();
            $table->decimal('preProduccion', 15, 2)->default(0)->nullable()->change();
            $table->decimal('postProduccion', 15, 2)->default(0)->nullable()->change();
            $table->decimal('copiado', 15, 2)->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Revertir a la precisión original
            $table->decimal('televisoras', 10, 6)->default(0)->nullable()->change();
            $table->decimal('radiodifusoras', 10, 6)->default(0)->nullable()->change();
            $table->decimal('cine', 10, 6)->default(0)->nullable()->change();
            $table->decimal('decdmx', 10, 6)->default(0)->nullable()->change();
            $table->decimal('deedos', 10, 6)->default(0)->nullable()->change();
            $table->decimal('deextr', 10, 6)->default(0)->nullable()->change();
            $table->decimal('revistas', 10, 6)->default(0)->nullable()->change();
            $table->decimal('mediosComplementarios', 10, 6)->default(0)->nullable()->change();
            $table->decimal('mediosDigitales', 10, 6)->default(0)->nullable()->change();
            $table->decimal('preEstudios', 10, 6)->default(0)->nullable()->change();
            $table->decimal('postEstudios', 10, 6)->default(0)->nullable()->change();
            $table->decimal('disenio', 10, 6)->default(0)->nullable()->change();
            $table->decimal('produccion', 10, 6)->default(0)->nullable()->change();
            $table->decimal('preProduccion', 10, 6)->default(0)->nullable()->change();
            $table->decimal('postProduccion', 10, 6)->default(0)->nullable()->change();
            $table->decimal('copiado', 10, 6)->default(0)->nullable()->change();
        });
    }
};
