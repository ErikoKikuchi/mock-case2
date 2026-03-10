<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RequestItem;
use App\Models\AttendanceRequest;
use App\Models\Attendance;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RequestItemFactory extends Factory
{
    protected $model =RequestItem::class;

    public function definition(): array
    {
        return [
            'request_id'    => AttendanceRequest::factory(),
            'attendance_id' => Attendance::factory(),
            'break_id'      => null,
            'column_name'   => $this->faker->randomElement([
                'clock_in', 'clock_out', 'break_start', 'break_end'
            ]),
            'before_value'  => now()->subHours(2),
            'after_value'   => now()->subHours(1),
        ];
    }
    public function forBreak(int $breakId): static
    {
        return $this->state(fn() => [
            'break_id'    => $breakId,
            'column_name' => $this->faker->randomElement(['break_start', 'break_end']),
        ]);
    }
}
