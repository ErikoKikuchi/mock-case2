<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        if($user->role === 'admin'){
            return redirect('/admin/attendance/list');
        }else{
            return redirect()->route('attendance');
        }
    }
}
