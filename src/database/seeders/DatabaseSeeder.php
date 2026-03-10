<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest ;
use App\Models\RequestItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);

        $admin = User::where('role', 'admin')->first();
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            $workDates = collect(range(1, 30))
                ->map(fn($i) => Carbon::today()->subDays($i))
                ->filter(fn() => fake()->boolean(80));
            foreach ($workDates as $date) {
                $attendance =Attendance::factory()
                    ->forDate($date)
                    ->create(['user_id' => $user->id]);
            }

            // 休憩パターン振り分け
            $pattern = fake()->randomElement(['none', 'one', 'two']);
            if ($pattern === 'one') {
                BreakTime::factory()
                    ->firstBreak(Carbon::parse($attendance->clock_in))
                    ->create(['attendance_id' => $attendance->id]);
            }
            if ($pattern === 'two') {
                BreakTime::factory()->firstBreak(Carbon::parse($attendance->clock_in))
                    ->create(['attendance_id' => $attendance->id]);
                BreakTime::factory()->secondBreak(Carbon::parse($attendance->clock_in))
                    ->create(['attendance_id' => $attendance->id]);
            }
            // 申請データ（トランザクション）
            // 過去データの一部に申請を作成
            if (fake()->boolean(30)) {
                DB::transaction(function () use ($user, $attendance, $admin) {
                    $isPending = fake()->boolean(50);
                    $request = AttendanceRequest::factory()
                        ->when(!$isPending, fn($f) => $f->approved($admin->id))
                        ->create([
                            'user_id' => $user->id,
                            'reason'  => fake()->randomElement([
                                '打刻忘れのため修正申請します',
                                '誤った時間で打刻したため修正します',
                                'システムエラーにより打刻できませんでした',
                            ]),
                        ]);
                    // 複数申請アイテム（1〜3件）
                    $itemCount = fake()->numberBetween(1, 3);
                    for ($j = 0; $j < $itemCount; $j++) {
                        RequestItem::factory()->create([
                            'request_id'    => $request->id,
                            'attendance_id' => $attendance->id,
                            'column_name'   => fake()->randomElement([
                                'clock_in', 'clock_out'
                            ]),
                            'before_value'  => $attendance->clock_in,
                            'after_value'   => $attendance->clock_in->copy()->addMinutes(
                                fake()->numberBetween(5, 30)
                            ),
                        ]);
                    }
                });
            }
        }
    }
}
