<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_url', 500);
            $table->string('thumbnail_url', 500)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_primary');
        });

        Schema::create('hobbies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('icon', 100)->nullable();
            $table->string('category', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_hobbies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hobby_id')->constrained()->onDelete('cascade');
            $table->unique(['user_id', 'hobby_id']);
            
            $table->index('user_id');
            $table->index('hobby_id');
        });

        Schema::create('user_swipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('swiper_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('swiped_id')->constrained('users')->onDelete('cascade');
            $table->enum('action', ['like', 'pass', 'super_like']);
            $table->timestamps();
            $table->unique(['swiper_id', 'swiped_id']);
            
            $table->index('swiper_id');
            $table->index('swiped_id');
            $table->index('action');
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
            $table->boolean('user_one_super_like')->default(false);
            $table->boolean('user_two_super_like')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('unmatched_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('unmatched_at')->nullable();
            $table->timestamps();
            $table->unique(['user_one_id', 'user_two_id']);
            
            $table->index('user_one_id');
            $table->index('user_two_id');
            $table->index('uuid');
            $table->index('is_active');
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->string('last_message_preview', 255)->nullable();
            $table->timestamps();
            
            $table->index('match_id');
            $table->index('last_message_at');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->enum('message_type', ['text', 'image', 'gif', 'audio'])->default('text');
            $table->text('message_content');
            $table->string('media_url', 500)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            
            $table->index('conversation_id');
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('created_at');
        });

        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_id')->constrained('users')->onDelete('cascade');
            $table->enum('reason', ['fake_profile', 'inappropriate_content', 'harassment', 'spam', 'underage', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'action_taken', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index('reporter_id');
            $table->index('reported_id');
            $table->index('status');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('plan', ['free', 'gold', 'platinum'])->default('free');
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_active');
            $table->index('expires_at');
        });

        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('identifier', 255);
            $table->enum('identifier_type', ['email', 'phone']);
            $table->string('otp_code', 10);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index('identifier');
            $table->index('user_id');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['match', 'message', 'like', 'super_like', 'profile_view', 'system']);
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('is_read');
            $table->index('created_at');
        });

        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('blocked_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['blocker_id', 'blocked_id']);
            
            $table->index('blocker_id');
            $table->index('blocked_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_blocks');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('user_reports');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('user_hobbies');
        Schema::dropIfExists('hobbies');
        Schema::dropIfExists('profile_images');
    }
};
