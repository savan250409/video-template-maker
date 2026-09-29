<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ User };
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;

class LoginController extends Controller
{
    public function login() {
        return view('Admin.auth.login');
    }
    public function authenticate(Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $rememberMe = $request->has('remember_me') ? true : false;
        if (Auth()->attempt(['email' => $request->email, 'password' => $request->password], $rememberMe)) {
            
            return redirect()->intended('admin/dashboard')->withSuccess('Welcome back');
        } else {
            return back()->with('error', 'Invalid email or password.');
        }
    }
    public function logout() {
        Session::flush();
        Auth::logout();
        return redirect()->route('login');
    }
    public function forbidden() {
        return view('forbidden');
    }
}
