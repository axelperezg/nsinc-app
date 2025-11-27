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
            // Cambiar la precisión de mediosDigitalesInternet de decimal(10, 6) a decimal(15, 2)
            // para que sea consistente con las demás columnas presupuestales
            $table->decimal('mediosDigitalesInternet', 15, 2)->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Revertir a la precisión original
            $table->decimal('mediosDigitalesInternet', 10, 6)->default(0)->nullable()->change();
        });
    }
};
