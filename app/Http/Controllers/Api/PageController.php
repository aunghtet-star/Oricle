<?php

namespace App\Http\Controllers\Api;
use App\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProfileResource;
use Laravel\Passport\TokenRepository;

class PageController extends Controller
{
    public function profile(){
        $user = Auth::user();
        
        $user = new ProfileResource($user);
        return success('success',$user);
    }

    
}
