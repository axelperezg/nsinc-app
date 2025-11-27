<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrar datos de coemisores a coemisores_acronyms si existe contenido
        DB::table('campaigns')
            ->whereNotNull('coemisores')
            ->where('coemisores', '!=', '')
            ->whereNull('coemisores_acronyms')
            ->update(['coemisores_acronyms' => DB::raw('coemisores')]);

        // Eliminar la columna coemisores
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('coemisores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->text('coemisores')->nullable()->after('institution_name');
        });
    }
};
