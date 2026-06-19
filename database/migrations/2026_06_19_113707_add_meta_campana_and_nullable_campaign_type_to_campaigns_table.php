<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->text('meta_campana')->nullable()->after('coemisores_acronyms');

            // Hacer campaign_type_id nullable para Promoción y Publicidad
            $table->dropForeign(['campaign_type_id']);
            $table->unsignedBigInteger('campaign_type_id')->nullable()->change();
            $table->foreign('campaign_type_id')->references('id')->on('campaign_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('meta_campana');

            $table->dropForeign(['campaign_type_id']);
            $table->unsignedBigInteger('campaign_type_id')->nullable(false)->change();
            $table->foreign('campaign_type_id')->references('id')->on('campaign_types')->onDelete('cascade');
        });
    }
};
