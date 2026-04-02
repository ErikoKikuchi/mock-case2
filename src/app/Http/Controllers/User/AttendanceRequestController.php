<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceChangeRequest;
use App\Models\AttendanceRequest;
use App\Models\Attendance;
use App\Services\StoreAttendanceRequestService;

class AttendanceRequestController extends Controller
{
//勤怠リクエストサービスクラス
    public function __construct(private StoreAttendanceRequestService $storeAttendanceRequestService) {}

//申請一覧表示
    public function index(Request $request){
        $user=Auth::user();
        $tab=$request->query('tab','pending');

        $requestItems=AttendanceRequest::with(['attendance', 'user'])
            ->where('user_id',$user->id)
            ->where('status',$tab)
            ->get();

        return view('request.index',compact('user','tab','requestItems'));
    }
//申請
    public function create(AttendanceChangeRequest $request){
        $attendance = Attendance::findOrFail($request->attendance_id);

        $this->storeAttendanceRequestService->storeAttendanceRequest($request,$attendance);

        return redirect()
            ->route('users.request.list')
            ->with('message', '変更申請が完了しました');
    }
}