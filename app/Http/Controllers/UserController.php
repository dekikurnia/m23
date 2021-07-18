<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $users = User::orderBy('id','ASC')->get();

        return view('users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $newUser = new User;

        $newUser->username = $request->get('username');
        $newUser->name = $request->get('name');
        //$newUser->email = $request->get('email');
        $newUser->password = Hash::make($request->get('password'));

        $newUser->save();
        $newUser->roles = $newUser->assignRole($request->get('roles'));
        return redirect()->route('users.index')->with('status-create', 'Tambah pengguna berhasil');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::all();
        $userRole = $user->roles->pluck('name','name')->all();
        return view('users.edit',compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateUserRequest $request, $id)
    {
        $user = User::find($id);
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if(!empty($request->has('password'))){ 
            $user->password = Hash::make($request->get('password'));

        } else {
            $user->password = Arr::except($request->get('password'));    
        }

        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->roles = $user->assignRole($request->get('roles'));
        return redirect()->route('users.index')->with('status-edit', 'Pengguna berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')->with('status-delete', 'Pengguna berhasil dihapus');
    }
}
