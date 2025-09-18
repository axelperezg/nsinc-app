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
        Schema::create('expiration_dates', function (Blueprint $table) {
            $table->id();

            $table->string('anio');
            $table->date('fecha_inicio');
            $table->date('fecha_diaPrevio');
            $table->date('fecha_limite');
            $table->date('fecha_restrictiva');
            $table->string('concept');
            $table->string('description');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expiration_dates');
    }
};
