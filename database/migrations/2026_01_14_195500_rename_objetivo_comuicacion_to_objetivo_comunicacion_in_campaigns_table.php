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
        // Renombrar la columna usando SQL directo (más compatible con MySQL)
        if (Schema::hasColumn('campaigns', 'objetivoComuicacion') && !Schema::hasColumn('campaigns', 'objetivoComunicacion')) {
            DB::statement('ALTER TABLE `campaigns` CHANGE `objetivoComuicacion` `objetivoComunicacion` TEXT NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el cambio
        if (Schema::hasColumn('campaigns', 'objetivoComunicacion') && !Schema::hasColumn('campaigns', 'objetivoComuicacion')) {
            DB::statement('ALTER TABLE `campaigns` CHANGE `objetivoComunicacion` `objetivoComuicacion` TEXT NOT NULL');
        }
    }
};
