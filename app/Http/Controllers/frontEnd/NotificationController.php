<?php

namespace App\Http\Controllers\frontEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(){
        $user = Auth::guard('web')->user();
        $notifications = $user->notifications()->paginate(5);
        //$notifications->paginate(5);
        return view('frontEnd.notification',compact('notifications'));
    }

    public function show($id){
        $user = Auth::guard('web')->user();
        $notification = $user->notifications()->where('id',$id)->firstOrFail();
        $notification->markAsRead();
        return view('frontEnd.notificationDetail',compact('notification'));
    }
}
