<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyManagerAuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials = $request->only('username', 'password');


        if (Auth::guard('company_manager')->attempt($credentials)) {
            // Logs store...

            Log::channel('login_log_companymanager')->info('Company Manager logged in.', ['username' => $request->username]);

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
    Log::channel('login_log_companymanager')->info('Company Manager log out.');

    return redirect()->route('company.manager.login.form')->with('status', 'Logged out successfully.');
}
}
