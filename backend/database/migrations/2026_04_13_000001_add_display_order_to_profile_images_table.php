<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_images', function (Blueprint $table) {
            $table->unsignedTinyInteger('display_order')->default(0)->after('is_primary');
        });

        // Backfill: set display_order based on existing row order per user
        DB::statement('
            UPDATE profile_images pi
            JOIN (
                SELECT id,
                       ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY is_primary DESC, id ASC) - 1 AS rn
                FROM profile_images
            ) ranked ON pi.id = ranked.id
            SET pi.display_order = ranked.rn
        ');
    }

    public function down(): void
    {
        Schema::table('profile_images', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};