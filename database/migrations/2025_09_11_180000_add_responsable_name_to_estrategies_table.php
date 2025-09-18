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
            if (!Schema::hasColumn('estrategies', 'responsable_name')) {
                $table->string('responsable_name')->nullable()->after('responsable_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            if (Schema::hasColumn('estrategies', 'responsable_name')) {
                $table->dropColumn('responsable_name');
            }
        });
    }
};
