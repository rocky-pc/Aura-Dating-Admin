<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_swipes', function (Blueprint $table) {
            $table->boolean('is_match')->default(false)->after('action');
        });
    }

    public function down(): void
    {
        Schema::table('user_swipes', function (Blueprint $table) {
            $table->dropColumn('is_match');
        });
    }
};
