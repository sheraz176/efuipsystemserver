<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminAuth extends Controller
{
    protected $redirectTo = '/superadmin/dashboard';

    public function showLoginForm()
    {
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('super_admin')->attempt($request->only('username', 'password'))) {
            $Superadmin = Auth::guard('super_admin')->user();
           
            session(['Superadmin' => $Superadmin]);
            return redirect()->route('superadmin.dashboard');

        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function showDashboard()
    {
        return view('superadmin.dashboard');
    }

    public function logout()
    {
        Auth::guard('super_admin')->logout();
        return redirect()->route('superadmin.login');
    }
}
