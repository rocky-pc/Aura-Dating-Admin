<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'non_binary', 'other']);
            $table->enum('interested_in', ['male', 'female', 'everyone'])->default('everyone');
            $table->text('bio')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->integer('max_distance')->default(50);
            $table->integer('min_age')->default(18);
            $table->integer('max_age')->default(100);
            $table->boolean('profile_completed')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
