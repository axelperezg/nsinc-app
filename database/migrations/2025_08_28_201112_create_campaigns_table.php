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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('temaEspecifico');
            $table->text('objetivoComunicacion');
            $table->foreignId('campaign_type_id')->constrained('campaign_types')->onDelete('cascade');
            $table->foreignId('estrategy_id')->constrained('estrategies')->onDelete('cascade');
            $table->string('institution_name')->nullable();
            $table->text('coemisores')->nullable();
            $table->string('sexo')->nullable();
            $table->string('edad')->nullable();
            $table->string('poblacion')->nullable();
            $table->string('nse')->nullable();
            $table->text('caracEspecific')->nullable();
            $table->boolean('tv_oficial')->default(false);
            $table->boolean('radio_oficial')->default(false);
            $table->boolean('tv_comercial')->default(false);
            $table->boolean('radio_comercial')->default(false);
            $table->decimal('televisoras', 10, 6)->default(0)->nullable();
            $table->decimal('radiodifusoras', 10, 6)->default(0)->nullable();
            $table->decimal('cine', 10, 6)->default(0)->nullable();
            $table->decimal('decdmx', 10, 6)->default(0)->nullable();
            $table->decimal('deedos', 10, 6)->default(0)->nullable();
            $table->decimal('deextr', 10, 6)->default(0)->nullable();
            $table->decimal('revistas', 10, 6)->default(0)->nullable();
            $table->decimal('mediosComplementarios', 10, 6)->default(0)->nullable();
            $table->decimal('mediosDigitales', 10, 6)->default(0)->nullable();
            $table->decimal('preEstudios', 10, 6)->default(0)->nullable();
            $table->decimal('postEstudios', 10, 6)->default(0)->nullable();
            $table->decimal('disenio', 10, 6)->default(0)->nullable();
            $table->decimal('produccion', 10, 6)->default(0)->nullable();
            $table->decimal('preProduccion', 10, 6)->default(0)->nullable();
            $table->decimal('postProduccion', 10, 6)->default(0)->nullable();
            $table->decimal('copiado', 10, 6)->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
