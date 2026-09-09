<?php

namespace Database\Factories;

use App\Enums\ApprovalStatus;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreProfile>
 */
class StoreProfileFactory extends Factory
{
    protected $model = StoreProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->storeOwner(),
            'store_name' => fake()->company().' Mart',
            'description' => fake()->sentence(),
            'status' => ApprovalStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => ApprovalStatus::Approved,
            'approved_at' => now(),
        ]);
    }
}
