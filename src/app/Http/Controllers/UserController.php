<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UserController extends Controller
{
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
        $date=$month ?Carbon::parse($month)->locale('ja'):Carbon::now()->locale('ja');

        $previous=$date->copy()->subMonth();
        $next=$date->copy()->addMonth();

        $start=$date->copy()->startOfMonth();
        $end=$date->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $monthlyAttendances=Attendance::where('user_id',$name->id)
        ->WhereBetween('work_date',[$start,$end])
        ->get();

        $calendar = collect();
        foreach($period as $date){
            $calendar->push([
                'date' => $date,
                'attendance' => $monthlyAttendances->first(fn($a) => $a->work_date->toDateString() === $date->toDateString())
            ]);
        }


        return view('admin.users.attendance',compact('date','monthlyAttendances','previous','next','calendar','name'));
    }
}
