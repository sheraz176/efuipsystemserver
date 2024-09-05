<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\CompanyManager;

class CompanyManagerAuthController extends Controller
{

    public function login(Request $request)
    {

        // dd($request->all());
        $credentials = $request->only('username', 'password');
        $company_managers = CompanyManager::where('username', $request->username)->first();
        // dd($company_managers);
        // Check if the agent exists and is active
        if ($company_managers) {

            if (Auth::guard('company_manager')->attempt($credentials)) {

                // Authentication successful, update login details and redirect to the agent dashboard
                $company_manager = Auth::guard('company_manager')->user();
                $company_manager->save();

                // Store agent information in the session
                session(['company_manager' => $company_manager]);

                return redirect()->route('company-manager-dashboard');
            } else {
                // Invalid credentials
                return redirect()->back()->withInput()->withErrors(['login' => 'Invalid credentials, Kindly check your username & password.']);
            }
        } else {
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
