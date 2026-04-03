<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest  as RequestModel;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\AttendanceService;
use App\Services\AttendanceStoreService;

class AttendanceController extends Controller
{
//サービスクラス
    public function __construct(
        private AttendanceService $attendanceService,
        private AttendanceStoreService $attendanceStoreService
        ) {}

//勤怠登録
    public function store(RequestModel $request){

        $action=$request->action;
        $this->attendanceStoreService->storeAttendance($action);
        return redirect()->route('attendance.show');
    }

//勤怠一覧表示
    public function index(Request $request){
        $user=Auth::user();
        $month=$request->query('month');

        $calendarData=$this->attendanceService->getCalendarData($month);

        $monthlyAttendances=Attendance::forUser($user->id)->inMonth($calendarData['start'],$calendarData['end'])->get();

        $calendar = $this->attendanceService->buildCalendar($calendarData['period'], $monthlyAttendances);

        return view('attendance.index',compact('calendarData','calendar'));
    }
//勤怠詳細表示
    public function show(Request $request, $id=null){
        $user=Auth::user();
        $userId=$user->id;
        $date=$request->query('date');

        $attendance=Attendance::findOrResolveByDate($id,$userId, $date);

        $breakTime=BreakTime::where('attendance_id',$attendance->id)->get();

        $attendanceRequest = AttendanceRequest::latestByAttendance($attendance);
        $attendanceRequest?->load('requestItems');

        return view ('attendance.detail',compact('user','attendance','attendanceRequest','breakTime'));
    }
}