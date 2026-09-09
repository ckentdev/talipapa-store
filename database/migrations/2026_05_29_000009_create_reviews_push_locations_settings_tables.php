<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reviewable');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'reviewer_id', 'reviewable_type', 'reviewable_id'], 'reviews_unique');
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('endpoint')->unique();
            $table->string('public_key');
            $table->string('auth_token');
            $table->string('content_encoding')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_profile_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['rider_profile_id', 'recorded_at']);
        });

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('rider_locations');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('reviews');
    }
};
