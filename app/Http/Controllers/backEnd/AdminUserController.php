<?php

namespace App\Http\Controllers\backEnd;

use App\AdminUser;
use Dotenv\Result\Success;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreAdminUser;
use App\Http\Requests\UpdateAdminUser;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backEnd.AdminUser.index');
    }

    public function ssd()
    {
        $data = AdminUser::query();
        return Datatables::of($data)
            ->editColumn('created_at', function ($each) {
                return Carbon::parse($each->created_at)->format('Y-m-d h-i-s');
            })
            ->editColumn('updated_at', function ($each) {
                return Carbon::parse($each->updated_at)->format('Y-m-d h-i-s');
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
                $edit_icon = '<a href="' . route('admin.users.edit', $each->id) . ' " class="text-warning"><i class="fas fa-edit"></i></a>';
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
        return view('backEnd.AdminUser.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdminUser $request)
    {
        $adminuser = new AdminUser();
        $adminuser->name = $request->name;
        $adminuser->email = $request->email;
        $adminuser->phone = $request->phone;
        $adminuser->password = Hash::make($request->password);
        $adminuser->save();
        return redirect()->route('admin.users.index')->with('create', 'Successfully created');
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
        $admin_user = AdminUser::findOrFail($id);
        return view('backEnd.AdminUser.edit', compact('admin_user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminUser $request, $id)
    {
        $adminuser = AdminUser::findOrFail($id);
        $adminuser->name = $request->name;
        $adminuser->email = $request->email;
        $adminuser->phone = $request->phone;
        $adminuser->password = $request->password ? Hash::make($request->password) : $adminuser->password;
        $adminuser->update();
        return redirect()->route('admin.users.index')->with('update', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $adminuser = AdminUser::findOrFail($id);
        $adminuser->delete();

        return 'success';
    }
}
