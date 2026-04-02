<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminAttendanceChangeRequest;
use App\Services\StoreAttendanceRequestService;
use App\Services\ApproveAttendanceService;
use App\Models\Attendance;
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
            ->route('each.staff.attendance', $attendance->user_id)
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
    public function show(Request $request)
    {
        $user = Auth::user();
        return view ('admin.request.approve',compact('user'));
    }
}