<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('whatsapp', 13)->nullable()->after('is_store');
        });

        // Keep the number that was hardcoded in the storefront before it became configurable.
        DB::table('teams')
            ->where('is_store', true)
            ->update(['whatsapp' => '5541995291049']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('whatsapp');
        });
    }
};
