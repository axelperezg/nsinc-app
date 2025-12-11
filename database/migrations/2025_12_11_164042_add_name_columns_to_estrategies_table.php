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
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('estrategies', 'institution_name')) {
                $table->string('institution_name')->nullable()->after('institution_id');
            }
            
            if (!Schema::hasColumn('estrategies', 'juridical_nature_name')) {
                $table->string('juridical_nature_name')->nullable()->after('juridical_nature_id');
            }
            
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
            if (Schema::hasColumn('estrategies', 'institution_name')) {
                $table->dropColumn('institution_name');
            }
            
            if (Schema::hasColumn('estrategies', 'juridical_nature_name')) {
                $table->dropColumn('juridical_nature_name');
            }
            
            if (Schema::hasColumn('estrategies', 'responsable_name')) {
                $table->dropColumn('responsable_name');
            }
        });
    }
};
