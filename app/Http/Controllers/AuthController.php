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

            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with("warning","Your account is pending approval or inactive.")->onlyInput("email");
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if (!in_array($user->role, ['head', 'fund_controller'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with("error","Your role has not been assigned yet. Please contact the ICT-OD.")->onlyInput("email");
            }

            if (!$user->division) {
                Auth::logout();

                return back()->with("error","No division is assigned to your account. Please contact the ICT-OD.")->onlyInput("email");
            }

            return redirect('/division/' . strtolower($user->division->name));

            // return $user->role === 'admin' ? redirect('/division'): redirect('/division/' . strtolower($user->division->name));
        }

        return back()->with('error', 'Invalid email or password')->onlyInput('email');
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            "name" => "required|min:3|max:50",
            "email" => "required|email|max:50|unique:users,email",
            "password" => "required|confirmed|min:8",
            "division_id" => "required|exists:divisions,id",

        ]);

        User::create($credentials);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'unassigned',
            'division_id' => $request->division_id,
            'status' => 'pending',
        ]);

        return redirect()->route('home')->with('success', "Account registered, Waiting for approval.");
    }

    public function showDivision()
    {
        $divisions = Division::all();
        return view('auth.register_user', compact('divisions'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
