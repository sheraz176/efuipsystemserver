<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyManagerAuthController extends Controller
{
    
    public function login(Request $request)
    {
        
        $credentials = $request->only('username', 'password');
        
        

        if (Auth::guard('company_manager')->attempt($credentials)) {
            // Authentication passed...
            return redirect()->route('company-manager-dashboard')->with('company_manager', Auth::guard('company_manager')->user());

        } else {
            // Authentication failed...
            return back()->withErrors(['username' => 'Invalid credentials']);
        }
    }

    public function showLoginForm()
    {
        return view('company_manager.login');
    }

    public function logout()
{
    Auth::guard('company_manager')->logout();

    return redirect()->route('company.manager.login.form')->with('status', 'Logged out successfully.');
}
}
