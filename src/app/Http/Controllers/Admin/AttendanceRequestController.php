<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminAttendanceChangeRequest;
use App\Services\StoreAttendanceRequestService;
use App\Services\ApproveAttendanceService;
use App\Models\Attendance;
use App\Models\User;
use App\Models\AttendanceRequest;

class AttendanceRequestController extends Controller
{
//勤怠リクエストサービスクラス
    public function __construct(
        private StoreAttendanceRequestService $storeAttendanceRequestService,
        private ApproveAttendanceService $approveAttendanceService,
        ){}

//管理者申請
    public function create(AdminAttendanceChangeRequest $request){
        $attendance = Attendance::findOrFail($request->attendance_id);

        $data=$this->storeAttendanceRequestService->storeAttendanceRequest($request,$attendance);

        $this->approveAttendanceService->approveAttendance($data);

        return redirect()
            ->route('each.staff.attendance',  [
                'id' => $attendance->user_id,
                'month' => $attendance->work_date->format('Y-m')
                ])
            ->with('message', '修正が完了しました');
    }

//管理者用申請一覧
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab=$request->query('tab','pending');

        $requestItems=AttendanceRequest::with(['attendance', 'user'])
            ->where('status',$tab)
            ->get();

        return view ('admin.request.index',compact('user','tab','requestItems'));
    }

//管理者用承認画面
    public function show(Request $request, $attendance_correct_request_id=null)
    {
        $user = Auth::user();

        $attendanceRequest=AttendanceRequest::findOrFail($attendance_correct_request_id);

        $attendance = Attendance::findOrFail($attendanceRequest->attendance_id);
        $user = User::findOrFail($attendance->user_id);

        $attendanceRequest?->load('requestItems');

        return view ('admin.request.approve',compact('user','attendance','attendanceRequest'));
    }

//管理者用承認処理
    public function update(Request $request)
    {
        $user = Auth::user();
        Attendance::findOrFail($request->attendance_id);
        $attendanceRequest = AttendanceRequest::findOrFail($request->attendance_request_id);

        if($attendanceRequest->status === 'approved'){
            return redirect()->back()->with('message', 'すでに承認済みです');
        }

        $this->approveAttendanceService->approveAttendance([
            'attendanceRequest' => $attendanceRequest,
            'approved_by'=>$user->id,
            ]);

        return redirect()
            ->route('request.list')
            ->with('message', '承認が完了しました');
    }
}