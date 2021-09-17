<?php

namespace App\Http\Controllers\backEnd;

use Exception;
use App\Wallet;
use App\AdminUser;
use App\Helpers\UUIDGenerator;
use Carbon\Carbon;
use Dotenv\Result\Success;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use Yajra\Datatables\Datatables;
use App\Http\Requests\UpdateUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreAdminUser;
use App\Http\Requests\UpdateAdminUser;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backEnd.User.index');
    }

    public function ssd()
    {
        $data = User::query();
        return Datatables::of($data)
            ->editColumn('created_at', function ($each) {
                return Carbon::parse($each->created_at)->format('Y-m-d H-i-s');
            })
            ->editColumn('updated_at', function ($each) {
                return Carbon::parse($each->updated_at)->format('Y-m-d H-i-s');
            })
            ->editColumn('user_agent', function ($each) {
                if ($each->user_agent) {
                    $agent = new Agent();
                    $agent->setUserAgent($each->user_agent);
                    $device = $agent->device();
                    $platform = $agent->platform();
                    $browser = $agent->browser();
                    return '<table class="table table-bordered">
                            <tbody>
                            <tr><td>Device</td><td>' . $device . '</td></tr>
                            <tr><td>Platform</td><td>' . $platform . '</td></tr>
                            <tr><td>Browser</td><td>' . $browser . '</td></tr>
                            </tbody>
                            </table>';
                }
                return '-';
            })
            ->rawColumns(['action', 'user_agent'])
            ->addColumn('action', function ($each) {
                $edit_icon = '<a href="' . route('admin.n_users.edit', $each->id) . ' " class="text-warning"><i class="fas fa-edit"></i></a>';
                $delete_icon = '<a href="" class="text-danger delete" data-id=" ' . $each->id . ' "><i class="fas fa-trash"></i></a>';
                return '<div class="icons">' . $edit_icon . $delete_icon . '</div>';
            })
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backEnd.User.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        DB::beginTransaction();

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            Wallet::firstOrCreate([
                'user_id' => $user->id
            ], [
                'account_numbers' => UUIDGenerator::AccountNumber(),
                'amount' => 0
            ]);
            DB::commit();

            return redirect()->route('admin.n_users.index')->with('create', 'Successfully created');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['fail' => 'something wrong'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $n_user = User::findOrFail($id);
        return view('backEnd.User.edit', compact('n_user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, $id)
    {
        DB::beginTransaction();

        try {
            $n_user = User::findOrFail($id);
            $n_user->name = $request->name;
            $n_user->email = $request->email;
            $n_user->phone = $request->phone;
            $n_user->password = $request->password ? Hash::make($request->password) : $n_user->password;
            $n_user->update();

            Wallet::firstOrCreate([
                'user_id' => $n_user->id
            ], [
                'account_numbers' => UUIDGenerator::AccountNumber(),
                'amount' => 0
            ]);
            DB::commit();

            return redirect()->route('admin.n_users.index')->with('update', 'Successfully updated');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['fail' => 'something wrong'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        $Wallet = Wallet::where('user_id',$id)->first();
        $Wallet->delete();
        
        return 'success';
    }
}
