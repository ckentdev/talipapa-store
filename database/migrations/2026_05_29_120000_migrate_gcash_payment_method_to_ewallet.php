<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('payment_method', 'gcash')
            ->update(['payment_method' => 'ewallet']);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('payment_method', 'ewallet')
            ->update(['payment_method' => 'gcash']);
    }
};
