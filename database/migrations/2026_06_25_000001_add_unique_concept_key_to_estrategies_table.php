<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            $table->string('unique_concept_key', 100)->nullable()->after('concepto');
        });

        // Poblar registros existentes; el primer registro de cada grupo obtiene la clave,
        // los duplicados previos (datos inválidos) quedan en NULL.
        $conceptosUnicos = ['Registro', 'Cancelacion'];

        DB::table('estrategies')
            ->whereIn('concepto', $conceptosUnicos)
            ->orderBy('id')
            ->get()
            ->each(function ($record) {
                $key = "{$record->institution_id}-{$record->anio}-{$record->partida_presupuestal}-{$record->concepto}";

                $alreadyUsed = DB::table('estrategies')
                    ->where('unique_concept_key', $key)
                    ->exists();

                if (!$alreadyUsed) {
                    DB::table('estrategies')
                        ->where('id', $record->id)
                        ->update(['unique_concept_key' => $key]);
                }
            });

        Schema::table('estrategies', function (Blueprint $table) {
            $table->unique('unique_concept_key');
        });
    }

    public function down(): void
    {
        Schema::table('estrategies', function (Blueprint $table) {
            $table->dropUnique(['unique_concept_key']);
            $table->dropColumn('unique_concept_key');
        });
    }
};
