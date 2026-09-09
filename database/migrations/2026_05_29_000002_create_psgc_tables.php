<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psgc_regions', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('psgc_provinces', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('region_code', 10);
            $table->string('name');
            $table->timestamps();

            $table->foreign('region_code')->references('code')->on('psgc_regions')->cascadeOnDelete();
            $table->index('region_code');
        });

        Schema::create('psgc_cities', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('province_code', 10);
            $table->string('name');
            $table->string('type')->nullable();
            $table->timestamps();

            $table->foreign('province_code')->references('code')->on('psgc_provinces')->cascadeOnDelete();
            $table->index('province_code');
        });

        Schema::create('psgc_barangays', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('city_code', 10);
            $table->string('name');
            $table->timestamps();

            $table->foreign('city_code')->references('code')->on('psgc_cities')->cascadeOnDelete();
            $table->index('city_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psgc_barangays');
        Schema::dropIfExists('psgc_cities');
        Schema::dropIfExists('psgc_provinces');
        Schema::dropIfExists('psgc_regions');
    }
};
