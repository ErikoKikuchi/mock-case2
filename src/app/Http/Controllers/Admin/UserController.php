<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Services\AttendanceService;

class UserController extends Controller
{
//カレンダーサービスクラス
    public function __construct(private AttendanceService $attendanceService) {}
//管理者用スタッフ一覧
    public function index(Request $request)
    {
        $user = Auth::user();
        $staffs=User::where('role','user')->get();
        return view ('admin.users.index',compact('staffs'));
    }
//管理者用各スタッフの勤怠一覧
    public function show(Request $request,$id)
    {
        $user = Auth::user();
        $name=User::find($id);
        $month=$request->query('month');

        $calendarData=$this->attendanceService->getCalendarData($month);

        $monthlyAttendances=Attendance::forUser($name->id)->inMonth($calendarData['start'],$calendarData['end'])->get();

        $calendar = $this->attendanceService->buildCalendar($calendarData['period'], $monthlyAttendances);

        return view('admin.users.attendance',compact('calendarData','calendar','name'));
    }
//CSV
    public function exportCsv(Request $request ,$id){
    //ユーザーを指定してデータの取得
        $query=User::findOrFail($id);
        $month=$request->query('month');

        $calendarData=$this->attendanceService->getCalendarData($month);

        $monthlyAttendances=Attendance::forUser($query->id)->inMonth($calendarData['start'],$calendarData['end'])->get();

        $calendar = $this->attendanceService->buildCalendar($calendarData['period'], $monthlyAttendances);

    //CSV設定
        $filename ="月次勤怠(" .$month ." 月分 ".$query->name .").csv";
        return response()->streamDownload(function () use ($calendar) {
            $stream = fopen('php://output', 'w');
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932');
            $csvHeader=['日付','出勤','退勤','休憩','合計'];
            fputcsv($stream, $csvHeader);

            foreach($calendar as $item){
                fputcsv($stream, [
                    $item['date']->locale('ja')->isoFormat('MM月DD日(ddd)'),
                    $item['attendance']?->clock_in?->format('H:i'),
                    $item['attendance']?->clock_out?->format('H:i'),
                    $item['attendance']?->breakTimeDisplay,
                    $item['attendance']?->workTimeDisplay,
                ]);
            }
            fclose($stream);
            }, $filename, [
            'Content-Type' => 'text/csv',
            ]);
    }
}