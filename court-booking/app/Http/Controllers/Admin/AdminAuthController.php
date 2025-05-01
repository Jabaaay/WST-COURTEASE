<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if the credentials match the admin credentials
        if ($credentials['email'] === config('admin.email') && 
            Hash::check($credentials['password'], config('admin.password'))) {
            
            // Store admin in session
            session([
                'admin_id' => 1,
                'admin_email' => $credentials['email'],
                'admin_name' => 'Super Admin'
            ]);
            
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id', 'admin_email', 'admin_name']);
        return redirect()->route('auth.login');
    }
} 