<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('status')->default('active')->after('phone');
            $table->boolean('sound_alerts_enabled')->default(false)->after('status');
            $table->boolean('location_permission')->default(false)->after('sound_alerts_enabled');
            $table->boolean('microphone_permission')->default(false)->after('location_permission');
            $table->boolean('push_permission')->default(false)->after('microphone_permission');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'status', 'sound_alerts_enabled',
                'location_permission', 'microphone_permission', 'push_permission',
            ]);
        });
    }
};
