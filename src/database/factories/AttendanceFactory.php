<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    private function generateTimeData(Carbon $date): array
    {
        $startHour   = fake()->numberBetween(8, 10);
        $startMinute = fake()->randomElement([0, 15, 30, 45]);
        $clockIn     = $date->copy()->setTime($startHour, $startMinute);
        $clockOut    = $clockIn->copy()->addHours(fake()->numberBetween(6, 10));

        if ($clockOut->hour >= 21) {
            $clockOut->setTime(21, 0);
        }

        return [
            'user_id'   => User::factory(),
            'work_date' => $date,
            'clock_in'  => $clockIn,
            'clock_out' => $clockOut,
        ];
    }

    public function definition(): array
    {
        return $this->generateTimeData(Carbon::today());
    }
    public function forDate(Carbon $date): static
    {
        return $this->state(fn() => $this->generateTimeData($date));
    }
}
