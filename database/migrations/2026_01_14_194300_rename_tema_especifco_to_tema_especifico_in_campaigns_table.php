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
        if (Schema::hasColumn('campaigns', 'temaEspecifco') && !Schema::hasColumn('campaigns', 'temaEspecifico')) {
            DB::statement('ALTER TABLE `campaigns` CHANGE `temaEspecifco` `temaEspecifico` TEXT NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el cambio
        if (Schema::hasColumn('campaigns', 'temaEspecifico') && !Schema::hasColumn('campaigns', 'temaEspecifco')) {
            DB::statement('ALTER TABLE `campaigns` CHANGE `temaEspecifico` `temaEspecifco` TEXT NOT NULL');
        }
    }
};
