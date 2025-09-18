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
            // Cambiar campos a JSON para manejar arrays
            $table->json('sexo')->change();
            $table->json('poblacion')->change();
            $table->json('nse')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Revertir a string
            $table->string('sexo')->change();
            $table->string('poblacion')->change();
            $table->string('nse')->change();
        });
    }
};
