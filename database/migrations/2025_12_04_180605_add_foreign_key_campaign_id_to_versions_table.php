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
        // Agregar foreign key a campaign_id en versions
        // Esta migración se ejecuta después de que ambas tablas (versions y campaigns) existan
        if (Schema::hasTable('versions') && Schema::hasTable('campaigns')) {
            try {
                Schema::table('versions', function (Blueprint $table) {
                    $table->foreign('campaign_id')
                        ->references('id')
                        ->on('campaigns')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // La foreign key ya existe o hay otro error, continuar
                // Esto puede pasar si la migración se ejecuta múltiples veces
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('versions')) {
            Schema::table('versions', function (Blueprint $table) {
                $table->dropForeign(['campaign_id']);
            });
        }
    }
};
