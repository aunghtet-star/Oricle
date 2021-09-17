<?php

namespace App\Http\Controllers\backEnd;

use Exception;
use App\Wallet;
use Carbon\Carbon;
use App\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerator;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        return view('backEnd.Wallet.index');
    }
    public function ssd()
    {
        $data = Wallet::with('user');
        return Datatables::of($data)
            ->addColumn('account_person', function ($each) {

                $user = $each->user;
                if ($user) {
                    return '<p>Name :' . $user->name . ' </p><p>mail:' . $user->email . ' </p><p>Phone :' . $user->phone . ' </p>';
                }
            })
            ->editColumn('created_at', function ($each) {
                return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function ($each) {
                return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('amount', function ($each) {
                return number_format($each->amount, 2);
            })
            ->rawColumns(['account_person'])
            ->make(true);
    }

    public function add(){
        $users = User::all();
        
        return view('backEnd.Wallet.Add',compact('users'));
    }

    public function store(Request $request){
        $request->validate([
            'user_id'=>'required',
            'amount'=>'required'
        ],[
            'user_id.required'=>'Please choose user'
        ]);
        $amount = $request->amount;
        $description = $request->description;

        $wallet = Wallet::where('user_id',$request->user_id)->firstOrFail();
        $to_account = User::where('id',$request->user_id)->firstOrFail();
        $to_account_wallet = $wallet;
        
        if($amount<1){
            return back()->withErrors(['amount'=>'This amount must be larger than 1 MMK'])->withInput();
        }
        DB::beginTransaction();
        try{
          
            $to_account_wallet->increment('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerator::RefNumber();
            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerator::TrxId();
            $to_account_transaction->user_id = $request->user_id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount ;
            $to_account_transaction->source_id = 0 ;
            $to_account_transaction->description = $description;
            $to_account_transaction->save(); 
            DB::commit();

            return redirect('admin/wallet')->with('create','successfully added');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect('/admin/wallet/add')->withErrors(['fail','Something wrong'.$e->getMessage()])->withInput();

        }
        
    }

    public function reduce(){
        $users = User::all();
        
        return view('backEnd.Wallet.Reduce',compact('users'));
    }

    public function remove(Request $request){
        $request->validate([
            'user_id'=>'required',
            'amount'=>'required'
        ],[
            'user_id.required'=>'Please choose user'
        ]);
        $amount = $request->amount;
        $description = $request->description;

        $wallet = Wallet::where('user_id',$request->user_id)->firstOrFail();
        //$to_account = User::where('id',$request->user_id)->firstOrFail();
        $to_account_wallet = $wallet;
        
        if($amount<1){
            return back()->withErrors(['amount'=>'This amount must be larger than 1 MMK'])->withInput();
        }
        DB::beginTransaction();
        try{
          
            $to_account_wallet->decrement('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerator::RefNumber();
            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerator::TrxId();
            $to_account_transaction->user_id = $request->user_id;
            $to_account_transaction->type = 2;
            $to_account_transaction->amount = $amount ;
            $to_account_transaction->source_id = 0 ;
            $to_account_transaction->description = $description;
            $to_account_transaction->save(); 
            DB::commit();

            return redirect('admin/wallet')->with('create','successfully reduced');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect('/admin/wallet/reduce')->withErrors(['fail','Something wrong'.$e->getMessage()])->withInput();

        }
        
    }
}
