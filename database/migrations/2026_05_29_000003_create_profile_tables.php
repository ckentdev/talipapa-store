<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('preferences')->nullable();
            $table->timestamps();
        });

        Schema::create('store_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_type');
            $table->string('plate_number')->nullable();
            $table->string('status')->default('pending');
            $table->string('availability')->default('unavailable');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_profiles');
        Schema::dropIfExists('store_profiles');
        Schema::dropIfExists('customer_profiles');
    }
};
