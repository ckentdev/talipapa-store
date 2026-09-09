<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PsgcSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('psgc_regions')->upsert([
            ['code' => '130000000', 'name' => 'National Capital Region (NCR)', 'created_at' => $now, 'updated_at' => $now],
        ], ['code'], ['name', 'updated_at']);

        $provinces = [
            ['code' => '133900000', 'region_code' => '130000000', 'name' => 'NCR, City of Manila'],
            ['code' => '137400000', 'region_code' => '130000000', 'name' => 'NCR, Second District'],
            ['code' => '137500000', 'region_code' => '130000000', 'name' => 'NCR, Third District'],
            ['code' => '137600000', 'region_code' => '130000000', 'name' => 'NCR, Fourth District'],
        ];

        DB::table('psgc_provinces')->upsert(
            array_map(fn (array $province) => [...$province, 'created_at' => $now, 'updated_at' => $now], $provinces),
            ['code'],
            ['region_code', 'name', 'updated_at']
        );

        $cities = [
            ['code' => '137401000', 'province_code' => '133900000', 'name' => 'City of Manila', 'type' => 'HUC'],
            ['code' => '137402000', 'province_code' => '137600000', 'name' => 'City of Mandaluyong', 'type' => 'HUC'],
            ['code' => '137403000', 'province_code' => '137400000', 'name' => 'City of Marikina', 'type' => 'HUC'],
            ['code' => '137404000', 'province_code' => '137600000', 'name' => 'Quezon City', 'type' => 'HUC'],
            ['code' => '137405000', 'province_code' => '137400000', 'name' => 'City of Pasig', 'type' => 'HUC'],
            ['code' => '137406000', 'province_code' => '137600000', 'name' => 'City of San Juan', 'type' => 'HUC'],
            ['code' => '137407000', 'province_code' => '137500000', 'name' => 'Caloocan City', 'type' => 'HUC'],
            ['code' => '137408000', 'province_code' => '137500000', 'name' => 'Malabon City', 'type' => 'HUC'],
            ['code' => '137409000', 'province_code' => '137500000', 'name' => 'Navotas City', 'type' => 'HUC'],
            ['code' => '137410000', 'province_code' => '137500000', 'name' => 'Valenzuela City', 'type' => 'HUC'],
            ['code' => '137411000', 'province_code' => '137600000', 'name' => 'Las Piñas City', 'type' => 'HUC'],
            ['code' => '137412000', 'province_code' => '137600000', 'name' => 'City of Makati', 'type' => 'HUC'],
            ['code' => '137413000', 'province_code' => '137600000', 'name' => 'Muntinlupa City', 'type' => 'HUC'],
            ['code' => '137414000', 'province_code' => '137600000', 'name' => 'Parañaque City', 'type' => 'HUC'],
            ['code' => '137415000', 'province_code' => '137600000', 'name' => 'Pasay City', 'type' => 'HUC'],
            ['code' => '137416000', 'province_code' => '137400000', 'name' => 'Pateros', 'type' => 'Municipality'],
            ['code' => '137417000', 'province_code' => '137400000', 'name' => 'Taguig City', 'type' => 'HUC'],
        ];

        DB::table('psgc_cities')->upsert(
            array_map(fn (array $city) => [...$city, 'created_at' => $now, 'updated_at' => $now], $cities),
            ['code'],
            ['province_code', 'name', 'type', 'updated_at']
        );

        $barangays = [
            // Quezon City
            ['code' => '137404001', 'city_code' => '137404000', 'name' => 'Alicia'],
            ['code' => '137404015', 'city_code' => '137404000', 'name' => 'Batasan Hills'],
            ['code' => '137404022', 'city_code' => '137404000', 'name' => 'Cubao'],
            ['code' => '137404031', 'city_code' => '137404000', 'name' => 'Diliman'],
            ['code' => '137404054', 'city_code' => '137404000', 'name' => 'Commonwealth'],
            // City of Manila
            ['code' => '137401001', 'city_code' => '137401000', 'name' => 'Barangay 1'],
            ['code' => '137401020', 'city_code' => '137401000', 'name' => 'Binondo'],
            ['code' => '137401038', 'city_code' => '137401000', 'name' => 'Malate'],
            ['code' => '137401049', 'city_code' => '137401000', 'name' => 'Ermita'],
            ['code' => '137401057', 'city_code' => '137401000', 'name' => 'Intramuros'],
        ];

        DB::table('psgc_barangays')->upsert(
            array_map(fn (array $barangay) => [...$barangay, 'created_at' => $now, 'updated_at' => $now], $barangays),
            ['code'],
            ['city_code', 'name', 'updated_at']
        );
    }
}
