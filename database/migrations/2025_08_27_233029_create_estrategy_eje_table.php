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
        Schema::create('estrategy_eje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estrategy_id')->constrained('estrategies')->onDelete('cascade');
            $table->foreignId('eje_id')->constrained('ejes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estrategy_eje');
    }
};
