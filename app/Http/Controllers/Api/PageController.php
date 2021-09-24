<?php

namespace App\Http\Controllers\Api;
use App\User;
use Exception;
use App\Wallet;
use App\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerator;
use App\Http\Requests\Transfer;
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\NotificationDetailResource;

class PageController extends Controller
{
    public function profile(){
        $user = Auth::user();
        
        $user = new ProfileResource($user);
        return success('success',$user);
    }

    public function transaction(Request $request){
        $user = Auth()->user();
        $transactions = Transaction::with('user','source')->orderBy('created_at','DESC')->where('user_id',$user->id);
        
        if($request->date){
            $transactions = Transaction::whereDate('created_at',$request->date);
        }

        if($request->type){
            $transactions = Transaction::where('type',$request->type);
        }
        $transactions = $transactions->paginate(5);
  
        $transactions = TransactionResource::collection($transactions)->additional(['result'=>1 , 'message'=>'success']);
        return $transactions;
    }

    public function transactionDetail($id){
        $user = Auth()->user();
        $transaction =  Transaction::where('user_id',$user->id)->where('trx_id',$id)->first();
        $transaction = new TransactionDetailResource($transaction);
        return success('transactionDetail',$transaction);
    }

    public function notification(){
        $user = Auth()->user();
        $notifications = $user->notifications()->paginate(5);
        $notifications = NotificationResource::collection($notifications)->additional(['result'=>1,'message'=>'success']);
        return $notifications;
    }

    public function notificationDetail($id){
        $user = Auth()->user();
        $notification = $user->notifications()->where('id',$id)->firstOrFail();
        //$notification = $notification->markAsRead();

        $notification = new NotificationDetailResource($notification);

        return success('success',$notification);

    }

    public function toaccountVerify(Request $request){
        $authUser = Auth()->user();
        if($authUser->phone != $request->phone){
            $user = User::where('phone',$request->phone)->first();
            if($user){
                return success('success',['name'=>$user->name,'phone'=>$user->phone]);
            }
        }
        return fail('something wrong',null);
    }

    public function transferConfirm(Transfer $request){
        $to_account = User::where('phone',$request->phone)->first();
        $from_account = Auth()->user();

        $phone = $request->phone;
        $amount = $request->amount;
        $description = $request->description;
        
        
        if($request->amount < 1){
            return fail('This amount must be larger than 1 MMK',null);
        }
        if($from_account->phone == $request->phone){
            return fail('Not transfer to YourSelf',null);
        }
        if(!$to_account){
            return fail('This account is no user',null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('You have not sufficient balance',null);
        }

        return success('success',[
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,
            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,
            'amount' => $amount,
            'description' => $description
        ]);
    }

    public function TransferComplete(Transfer $request){
        $from_account = Auth()->user();

        if(!$request->password){
            return fail('Please fill your password',null);
        }
        if (!Hash::check( $request->password , $from_account->password)) {
            return fail('The passsword is incorrect',null);
        }


        $to_account = User::where('phone',$request->phone)->first();
        $from_account = Auth()->user();

        $phone = $request->phone;
        $amount = $request->amount;
        $description = $request->description;

        if($request->amount < 1){
            return fail('This amount must be larger than 1 MMK',null);
        }
        if($from_account->phone == $request->phone){
            return fail('Not transfer to YourSelf',null);
        }
        if(!$to_account){
            return fail('This account is no user',null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('You have not sufficient balance',null);
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return fail('Something went wrong',null);
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
            
            return success('successfully transferred',['trx_id' => $from_account_transaction->trx_id ]);
            return redirect('/transactionDetail/'.$from_account_transaction->trx_id)->with('transfer_success','Done');

        }catch(Exception $e){

            DB::rollBack();
            return fail('Something went wrong!' ,null);
        }
    }

    public function scanAndPayConfirm(Transfer $request){
        $to_account = User::where('phone',$request->phone)->first();
        if(!$to_account){
            return fail('Scan is not work,this phone number is not defined',null);
        }
        $from_account = Auth()->user();

        $phone = $request->phone;
        $amount = $request->amount;
        $description = $request->description;
        
        
        if($request->amount < 1){
            return fail('This amount must be larger than 1 MMK',null);
        }
        if($from_account->phone == $request->phone){
            return fail('Not transfer to YourSelf',null);
        }
        if(!$to_account){
            return fail('This account is no user',null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('You have not sufficient balance',null);
        }

        return success('success',[
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,
            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,
            'amount' => $amount,
            'description' => $description
        ]);
    }

    public function scanAndPayComplete(Transfer $request){
        $from_account = Auth()->user();

        if(!$request->password){
            return fail('Please fill your password',null);
        }
        if (!Hash::check( $request->password , $from_account->password)) {
            return fail('The passsword is incorrect',null);
        }


        $to_account = User::where('phone',$request->phone)->first();
        $from_account = Auth()->user();

        $phone = $request->phone;
        $amount = $request->amount;
        $description = $request->description;

        if($request->amount < 1){
            return fail('This amount must be larger than 1 MMK',null);
        }
        if($from_account->phone == $request->phone){
            return fail('Not transfer to YourSelf',null);
        }
        if(!$to_account){
            return fail('This account is no user',null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('You have not sufficient balance',null);
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return fail('Something went wrong',null);
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
            
            return success('successfully transferred',['trx_id' => $from_account_transaction->trx_id ]);
            return redirect('/transactionDetail/'.$from_account_transaction->trx_id)->with('transfer_success','Done');

        }catch(Exception $e){

            DB::rollBack();
            return fail('Something went wrong!' ,null);
        }
    }
}
