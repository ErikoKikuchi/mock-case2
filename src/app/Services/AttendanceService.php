<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function getCalendarData(string|null $month): array 
    {
        $date=$month ?Carbon::parse($month)->locale('ja'):Carbon::now()->locale('ja');

        $previous=$date->copy()->subMonth();
        $next=$date->copy()->addMonth();

        $start=$date->copy()->startOfMonth();
        $end=$date->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        return [
            'date'=>$date,
            'previous'=>$previous,
            'next'=>$next,
            'start'=>$start,
            'end'=>$end,
            'period'=>$period];
    }

    public function buildCalendar(CarbonPeriod $period, Collection $attendances):Collection
    {
        return collect($period)->map(fn($date) => [
            'date'       => $date,
            'attendance' => $attendances->first(
                fn($a) => $a->work_date->toDateString() === $date->toDateString()
            ),
        ]);
    }
    public function getDailyData(string|null $day): array
    {
        $date = $day ? Carbon::parse($day)->locale('ja') : Carbon::now()->locale('ja');

        return [
            'date'     => $date,
            'previous' => $date->copy()->subDay(),
            'next'     => $date->copy()->addDay(),
        ];
    }
    public function makeDailyCalendar(Collection $staffs, Collection $attendances):Collection
    {
        return $staffs->map(fn($staff) => [
            'staff'       => $staff,
            'attendance' => $attendances->firstWhere('user_id', $staff->id)
        ]);
    }
}
