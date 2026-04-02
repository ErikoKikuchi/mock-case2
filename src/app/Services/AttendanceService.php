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
}
