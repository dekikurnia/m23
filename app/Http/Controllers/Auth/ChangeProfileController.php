<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeProfileRequest;
use App\Models\User;

class ChangeProfileController extends Controller
{
    public function index()
    {
        return view('auth.change-profile');
    } 

    public function store(ChangeProfileRequest $request)
    {
        User::find(auth()->user()->id)->update(
            [
                'username'=> $request->get('username'),
                'name'=> $request->get('name')
            ]);
        return redirect()->back()->with('status-edit', 'Ubah profil pengguna berhasil');
    }
}
