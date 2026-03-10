<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AttendanceRequest;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class AttendanceRequestFactory extends Factory
{
    protected $model = AttendanceRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'reason'      => $this->faker->sentence(),
            'status'      => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ];
    }
    public function approved(int $adminId): static
    {
        return $this->state(fn() => [
            'status'      => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
        ]);
    }
}
