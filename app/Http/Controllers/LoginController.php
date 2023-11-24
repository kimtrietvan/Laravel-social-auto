<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if(Auth::attempt($credentials, isset($request->remember)))
        {
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');

    }

    public function register(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);

        if ($request->password !== $request->confirm_password) {
            return redirect()->back()->withErrors(['password' => 'Password not match']);
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
//        $request->session()->regenerate();
        return redirect()->route('login');



    }
}
