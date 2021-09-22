<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Wallet;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string',  'unique:users'],
            'password' => ['required', 'string', 'min:8']
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->ip = $request->ip();
        $user->user_agent = $request->server('HTTP_USER_AGENT');
        $user->login_at = date('Y-m-d H:i:s');
        $user->save();

        Wallet::firstOrCreate([
            'user_id' => $user->id
        ], [
            'account_numbers' => UUIDGenerator::AccountNumber(),
            'amount' => 0
        ]);  
        $token = $user->createToken('Oricle Pay')->accessToken;

        return success('Successfully register',['token'=>$token]);

    }

    public function login(Request $request){
        $request->validate([
            'phone' => ['required', 'string', ],
            'password' => ['required', 'string',]
        ]);
        
        if(Auth::attempt(['phone'=>$request->phone,'password'=>$request->password])){
            $user = Auth()->user();

            Wallet::firstOrCreate([
                'user_id' => $user->id
            ], [
                'account_numbers' => UUIDGenerator::AccountNumber(),
                'amount' => 0
            ]);  

            $token = $user->createToken('Oricle Pay')->accessToken;

            return success('Successfully login',['token'=>$token]);
        }
        return fail('These cretials are not correct',null);

    }

    public function logout(){
        $user = Auth()->user();
        $token = $user->token();
        $tokenRepository = app(TokenRepository::class);
        $tokenRepository->revokeAccessToken($token);

        return success('success logout',null);
    }
  
}
