<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'break_start'   => null,
            'break_end'     => null,
        ];
    }
    public function firstBreak(Carbon $clockIn): static
    {
        return $this->state(fn() => [
            'break_start' => $clockIn->copy()->addHours(3)->toTimeString(),
            'break_end'   => $clockIn->copy()->addHours(3)->addMinutes(60)->toTimeString(),
        ]);
    }
    public function secondBreak(Carbon $clockIn): static
    {
        return $this->state(fn() => [
            'break_start' => $clockIn->copy()->addHours(6)->toTimeString(),
            'break_end'   => $clockIn->copy()->addHours(6)->addMinutes(30)->toTimeString(),
        ]);
    }
}
