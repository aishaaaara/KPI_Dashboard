<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([

            'email' => 'required|email',

            'password' => 'required'

        ]);

        $credentials = [

            'email' => $request->email,

            'password' => $request->password

        ];

        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();

            return redirect('/admin/dashboard');
        }

        return back()->with(

            'error',

            'Email atau password salah'

        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}