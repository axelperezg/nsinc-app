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
        Schema::table('expiration_dates', function (Blueprint $table) {
            $table->string('target_role')->nullable()->after('concept');
            $table->json('exempted_coordinator_ids')->nullable()->after('exempted_institution_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expiration_dates', function (Blueprint $table) {
            $table->dropColumn(['target_role', 'exempted_coordinator_ids']);
        });
    }
};
