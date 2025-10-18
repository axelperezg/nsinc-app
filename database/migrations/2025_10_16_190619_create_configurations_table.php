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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Clave única de configuración');
            $table->string('value')->nullable()->comment('Valor de la configuración');
            $table->string('type')->default('boolean')->comment('Tipo de dato: boolean, string, integer, json');
            $table->string('group')->default('general')->comment('Grupo de configuración');
            $table->string('label')->comment('Etiqueta descriptiva');
            $table->text('description')->nullable()->comment('Descripción de la configuración');
            $table->timestamps();

            $table->index('key');
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
