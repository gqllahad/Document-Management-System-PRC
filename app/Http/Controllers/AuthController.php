<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Division;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => "required|email|max:50",
            "password" => "required",
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect("/division");
        }

        return back()->withErrors(["email" => "Invalid email or password"])->onlyInput("email");
    }

    public function register(Request $request){
        $credentials = $request->validate([
            "name" => "required|min:3|max:50",
            "email" => "required|email|max:50|unique:users,email",
            "password" => "required|confirmed|min:8",
            "division_id" => "required|exists:divisions,id",
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'division_id' => $request->division_id,
        ]);

        return redirect()->route('home');
    }

    public function showDivision(){
        $divisions = Division::all();
        return view('auth.register_user', compact('divisions'));
    }
}
