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
            // Verificar si la columna ya existe antes de agregarla
            if (!Schema::hasColumn('estrategies', 'oficio_dgnc')) {
                $table->string('oficio_dgnc')->nullable()->after('concepto');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            if (Schema::hasColumn('estrategies', 'oficio_dgnc')) {
                $table->dropColumn('oficio_dgnc');
            }
        });
    }
};
