<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: add new nullable columns
        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('periodo_fin');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->boolean('sin_plan_publicado')->default(false)->after('activo');
        });

        // Step 2: migrate existing integer data to dates
        DB::table('plan_nacional_desarrollo')->get()->each(function ($record) {
            DB::table('plan_nacional_desarrollo')->where('id', $record->id)->update([
                'fecha_inicio' => $record->periodo_inicio.'-01-01',
                'fecha_fin' => $record->periodo_fin.'-12-31',
            ]);
        });

        // Step 3: make date columns NOT NULL and ejes nullable
        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable(false)->change();
            $table->date('fecha_fin')->nullable(false)->change();
            $table->json('ejes_generales')->nullable()->change();
            $table->json('ejes_transversales')->nullable()->change();
        });

        // Step 4: drop old index and integer columns
        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->dropIndex(['periodo_inicio', 'periodo_fin']);
            $table->dropColumn(['periodo_inicio', 'periodo_fin']);
        });

        // Step 5: create new index on date columns
        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->integer('periodo_inicio')->nullable()->after('nombre');
            $table->integer('periodo_fin')->nullable()->after('periodo_inicio');
        });

        DB::table('plan_nacional_desarrollo')->get()->each(function ($record) {
            DB::table('plan_nacional_desarrollo')->where('id', $record->id)->update([
                'periodo_inicio' => (int) date('Y', strtotime($record->fecha_inicio)),
                'periodo_fin' => (int) date('Y', strtotime($record->fecha_fin)),
            ]);
        });

        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->integer('periodo_inicio')->nullable(false)->change();
            $table->integer('periodo_fin')->nullable(false)->change();
            $table->json('ejes_generales')->nullable(false)->change();
            $table->json('ejes_transversales')->nullable(false)->change();
        });

        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->dropIndex(['fecha_inicio', 'fecha_fin']);
            $table->dropColumn(['fecha_inicio', 'fecha_fin', 'sin_plan_publicado']);
        });

        Schema::table('plan_nacional_desarrollo', function (Blueprint $table) {
            $table->index(['periodo_inicio', 'periodo_fin']);
        });
    }
};
