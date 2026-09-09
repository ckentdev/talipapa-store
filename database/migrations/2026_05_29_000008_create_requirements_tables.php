<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_profile_id')->constrained()->cascadeOnDelete();
            $table->string('business_permit_path');
            $table->string('valid_id_path');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_profile_id')->constrained()->cascadeOnDelete();
            $table->string('valid_id_path');
            $table->string('driver_license_path');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_requirements');
        Schema::dropIfExists('store_requirements');
    }
};
