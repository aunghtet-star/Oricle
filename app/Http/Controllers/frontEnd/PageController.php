<?php

namespace App\Http\Controllers\frontEnd;

use App\User;
use Exception;
use App\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerator;
use App\Http\Requests\Transfer;
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\updatePassword;
use Illuminate\Support\Facades\Notification;

class PageController extends Controller
{
    public function home()
    {
        $user = Auth::guard('web')->user();
        
        return view('frontEnd.home',compact('user'));
    }
    public function profile(){
        $user = Auth::guard('web')->user();
        return view('frontEnd.profile',compact('user'));
    }
    public function updatePassword(){
        return view('frontEnd.updatePassword');
    }
    public function updatePasswordStore(updatePassword $request){
        
        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $user = Auth::guard('web')->user();

        if (Hash::check($old_password, $user->password)) {
            $user->password = Hash::make($new_password);
            $user->update();
            
            $title = 'Updated successfully';
            $message = 'Your account password is successfully changed';
            $sourceable_id = $user->id;
            $sourceable_type = User::class;
            $web_link = url('profile');
            $deep_link = [
                'target' => 'profile',
                'parameter' => null
            ];

        Notification::send([$user], new InvoicePaid($title,$message,$sourceable_id,$sourceable_type,$web_link,$deep_link));
            return redirect()->route('profile')->with('update','successfully updated');
            
        
        }
        return back()->withErrors(['old_password'=>'Your old password is not correct'])->withInput();
    }

    public function wallet(){
        $user = Auth::guard('web')->user();
        return view('frontEnd.wallet',compact('user'));
    }
    public function transfer(){
        $user = Auth::guard('web')->user();
        return view('frontEnd.transfer',compact('user'));
    }
    public function transferconfirm(Transfer $request){

        $to_account = User::where('phone',$request->phone)->first();
        $user = Auth::guard('web')->user();

        $phone = $request->phone;
        $amount = $request->amount;
        $description = $request->description;
        $from_account = $user;
        
        if($request->amount < 1){
            return back()->withErrors(['amount'=>'This amount must be larger than 1 MMK'])->withInput();
        }
        if($user->phone == $request->phone){
            return back()->withErrors(['phone'=>'Not transfer to YourSelf'])->withInput();
        }
        if(!$to_account){
            return back()->withErrors(['phone'=>'This account is no user'])->withInput();
        }
        if($from_account->wallet->amount < $amount){
            return back()->withErrors(['amount'=>'You have not sufficient balance'])->withInput();
        }
        return view('frontEnd.transferconfirm',compact('phone','amount','user','description','to_account'));
    }

    public function transfercomplete(Transfer $request){
       
       
        $to_account = User::where('phone',$request->phone)->first();
        $user = Auth::guard('web')->user();

        $phone = $request->phone;
        $amount = $request->amount;
        $description = $request->description;
        $from_account = $user;
        
        if($request->amount < 1){
            return back()->withErrors(['amount'=>'This amount must be larger than 1 MMK'])->withInput();
        }
        if($user->phone == $request->phone){
            return back()->withErrors(['phone'=>'Not transfer to YourSelf'])->withInput();
        }
        if(!$to_account){
            return back()->withErrors(['phone'=>'This account is no user'])->withInput();
        }
        if($from_account->wallet->amount < $amount){
            return redirect('/transfer')->withErrors(['amount'=>'You have not sufficient balance'])->withInput();
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail'=>'something wrong'])->withInput();
        }
        
        DB::beginTransaction();
        try{
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount',$amount);
            $from_account_wallet->update();
    
            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerator::RefNumber();
            $from_account_transaction = new Transaction();
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerator::TrxId();
            $from_account_transaction->user_id = $from_account->id ;
            $from_account_transaction->type = 2 ;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description ;
            $from_account_transaction->save(); 

            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerator::TrxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount ;
            $to_account_transaction->source_id = $from_account->id ;
            $to_account_transaction->description = $description;
            $to_account_transaction->save(); 

            $title = 'Successfully Transferred';
            $message = 'You have sent '.$to_account->name .  $to_account->phone .'to transfrred successfully';
            $sourceable_id = $to_account->id;
            $sourceable_type = Transfer::class;
            $web_link = url('/transactionDetail/'.  $from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $from_account_transaction->trx_id
                ]
            ];
        Notification::send([$from_account], new InvoicePaid($title,$message,$sourceable_id,$sourceable_type,$web_link,$deep_link));

            $title = 'Successfully Received';
            $message = 'You have receive from '.$from_account->name . $from_account->phone .'from receive successfully';
            $sourceable_id = $from_account->id;
            $sourceable_type = Transfer::class;
            $web_link = url('/transactionDetail/'.  $to_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ]
            ];
        Notification::send([$to_account], new InvoicePaid($title,$message,$sourceable_id,$sourceable_type,$web_link,$deep_link,$deep_link));
            
            DB::commit();
            
            
            return redirect('/transactionDetail/'.$from_account_transaction->trx_id)->with('transfer_success','Done');

        }catch(Exception $e){

            DB::rollBack();
            return redirect('/')->withErrors(['fail','Something wrong'.$e->getMessage()])->withInput();
        }
        
    }

    public function transaction(Request $request){
        $user = Auth::guard('web')->user();
        $transactions = Transaction::with('user','source')->orderBy('created_at','DESC')->where('user_id',$user->id);
        if($request->type){
            $transactions = $transactions->where('type',$request->type);
        }
        if($request->date){
            $transactions = $transactions->whereDate('created_at',$request->date);
        }
        $transactions = $transactions->paginate(5);
        return view('frontEnd.hello',compact('transactions'));
    }

    public function transactionDetail($trx_id){
        $user = Auth::guard('web')->user();
        $transaction = Transaction::with('user','source')->where('user_id',$user->id)->where('trx_id',$trx_id)->first();
        return view('frontEnd.transactionDetail',compact('transaction'));
    }
    public function password_check(Request $request){
        $authUser = Auth::guard('web')->user();
        if(!$request->password){
            return response()->json([
                'status'=>'fail',
                'message'=>'Please fill your password'
            ]);
        }
        if (Hash::check( $request->password , $authUser->password)) {
            return response()->json([
                'status'=>'success',
                'message'=>'The passsword is correct'
            ]);
        }else{
            return response()->json([
                'status'=>'fail',
                'message'=>'The passsword is incorrect'
            ]);
        }
    }
    public function toaccountVerify(Request $request){
        $user = Auth::guard('web')->user();
        if($user->phone != $request->phone){
            $checkto = User::where('phone',$request->phone)->first();
            if($checkto){
                return response()->json([
                    'status'=>'success',
                    'data'=>$checkto
                ]);
            }
        }
        return response()->json([
            'status'=>'fail',
            'message'=>'Invalid data'
        ]);
    }
   
    public function receive_qr(){
        $authUser = Auth::guard('web')->user();
        return view('frontEnd.receive_qr',compact('authUser'));
    }
    
    public function scanQr(){
        return view('frontEnd.scanQr');
    }

    public function scan_and_pay(Request $request){
        $from_account = Auth::guard('web')->user();
        $to_account = User::where('phone',$request->to_phone)->first();
        if(!$to_account){
            return back(['fail'=>'The QR is incorrect']);
        }
        return view('frontEnd.Scan',compact('to_account','from_account'));
    }
    
    public function scan_and_pay_confirm(Request $request){
        
        $request->validate([
            'amount'=>'required'
        ]);

        $to_account = User::where('phone',$request->to_phone)->first();
        $user = Auth::guard('web')->user();

        $phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $from_account = $user;
        
        if($request->amount < 1){
            return back()->withErrors(['amount'=>'This amount must be larger than 1 MMK'])->withInput();
        }
        if($user->phone == $request->phone){
            return back()->withErrors(['phone'=>'Not transfer to YourSelf'])->withInput();
        }
        if(!$to_account){
            return back()->withErrors(['phone'=>'This account is no user'])->withInput();
        }
        if($from_account->wallet->amount < $amount){
            return back()->withErrors(['amount'=>'You have not sufficient balance'])->withInput();
        }
        return view('frontEnd.scan_and_pay_confirm',compact('phone','amount','from_account','description','to_account'));
    }

    public function scan_and_pay_complete(Transfer $request){
        $to_account = User::where('phone',$request->phone)->first();
        
        $user = Auth::guard('web')->user();
        $to_account = User::where('phone',$request->phone)->first();
        $user = Auth::guard('web')->user();

        $phone = $request->phone;
        $amount = $request->amount;
        $description = $request->description;
        $from_account = $user;
        
        if($request->amount < 1){
            return back()->withErrors(['amount'=>'This amount must be larger than 1 MMK'])->withInput();
        }
        if($user->phone == $request->phone){
            return back()->withErrors(['phone'=>'Not transfer to YourSelf'])->withInput();
        }
        if(!$to_account){
            return back()->withErrors(['phone'=>'This account is no user'])->withInput();
        }
        if($from_account->wallet->amount < $amount){
            return redirect('/transfer')->withErrors(['amount'=>'You have not sufficient balance'])->withInput();
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail'=>'something wrong'])->withInput();
        }
        
        DB::beginTransaction();
        try{
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount',$amount);
            $from_account_wallet->update();
    
            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerator::RefNumber();
            $from_account_transaction = new Transaction();
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerator::TrxId();
            $from_account_transaction->user_id = $from_account->id ;
            $from_account_transaction->type = 2 ;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description ;
            $from_account_transaction->save(); 

            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerator::TrxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount ;
            $to_account_transaction->source_id = $from_account->id ;
            $to_account_transaction->description = $description;
            $to_account_transaction->save(); 

            $title = 'Successfully Transferred';
            $message = 'You have sent '.$to_account->name .  $to_account->phone .'to transfrred successfully';
            $sourceable_id = $to_account->id;
            $sourceable_type = Transfer::class;
            $web_link = url('/transactionDetail/'.  $from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $from_account_transaction->trx_id
                ]
            ];
        Notification::send([$from_account], new InvoicePaid($title,$message,$sourceable_id,$sourceable_type,$web_link,$deep_link));

            $title = 'Successfully Received';
            $message = 'You have receive from '.$from_account->name . $from_account->phone .'from receive successfully';
            $sourceable_id = $from_account->id;
            $sourceable_type = Transfer::class;
            $web_link = url('/transactionDetail/'.  $to_account_transaction->trx_id);

        Notification::send([$to_account], new InvoicePaid($title,$message,$sourceable_id,$sourceable_type,$web_link,$deep_link));
            DB::commit();
            
            return redirect('/transactionDetail/'.$from_account_transaction->trx_id)->with('transfer_success','Done');

        }catch(Exception $e){

            DB::rollBack();
            return back()->withErrors(['fail','Something wrong'.$e->getMessage()])->withInput();
        }
    }
}
