<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{

    

     public function manageUsers()
    {
        abort_unless(Auth::check(), 403);

        abort_unless(Auth::user()->role === 'admin', 403);

        $users = User::with('division')
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();

        
        return view('admin.manage_users', compact('users'));
    }


    public function updateRole(Request $request, User $user)
    {
        abort_if($user->role === 'admin', 403);

        $data = $request->validate([
            'role' => 'required|in:head,fund_controller',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update($data);

        return back()->with('success', 'Role updated for ' . $user->name . '.');
    }


    

}
