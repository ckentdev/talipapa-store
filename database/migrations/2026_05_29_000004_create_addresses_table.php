<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('addressable');
            $table->string('label')->nullable();
            $table->string('region_code', 10)->nullable();
            $table->string('province_code', 10)->nullable();
            $table->string('city_code', 10)->nullable();
            $table->string('barangay_code', 10)->nullable();
            $table->string('street_address');
            $table->string('postal_code')->nullable();
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
