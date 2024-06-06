<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company\CompanyProfile;
use Illuminate\Support\Facades\Hash;
use App\Models\CompanyManager;
use App\Models\SuperAgent\SuperAgentModel;
use Illuminate\Support\Facades\Validator;

class UserManageController extends Controller
{

    public function company_manager_create()
    {
        $companies = CompanyProfile::all();
        return view('superadmin.users-manage.create-company-manager',compact('companies'));
    }

    public function company_manager_index()
    {
        $companies_managers = CompanyManager::all();
        return view('superadmin.users-manage.company-manager-index',compact('companies_managers'));
    }

    public function company_manager_store(Request $request)
    {
        //  dd($request);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:tele_sales_agents',
            'email' => 'required|email|unique:tele_sales_agents',
            'company_id' => 'required',
            'cnic' => 'required',
            'phone_number' => 'required',
            'ip_address' => 'required',
            'password' => 'required|min:6', // Add validation for password
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $company_manager = new CompanyManager();
        $company_manager->first_name = $request->first_name;
        $company_manager->last_name = $request->last_name;
        $company_manager->username = $request->username;
        $company_manager->email = $request->email;
        $company_manager->cnic = $request->cnic;
        $company_manager->company_id = $request->company_id;
        $company_manager->phone_number = $request->phone_number;
        $company_manager->ip_address = $request->ip_address;
        $company_manager->password = bcrypt($request->password);
        $company_manager->save();


        return redirect()->route('superadmin.company_manager_index')->with('success', 'Company Manager created successfully.');
    }

    public function super_agent_create()
    {
        $companies = CompanyProfile::all();
        return view('superadmin.users-manage.create-super-agent',compact('companies'));
    }
    public function super_agent_index()
    {
        $super_agents = SuperAgentModel::get();
        return view('superadmin.users-manage.super-agent-index',compact('super_agents'));
    }
    public function super_agent_store(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:super_agents',
            'email' => 'required|email|unique:super_agents',
            'status' => 'required|in:1,0',
            'company_id' => 'required',
            'password' => 'required|min:6', // Add validation for password
        ]);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Set default values
        $validatedData = $validator->validated();
        $validatedData['islogin'] = 0;
        $validatedData['call_status'] = false;
        $validatedData['password'] = Hash::make($request->input('password'));
        $validatedData['today_login_time'] = now();
        $validatedData['today_logout_time'] = now();

        // Create SuperAgentModel record
        SuperAgentModel::create($validatedData);

        return redirect()->route('superadmin.super_agent_index')->with('success', 'Super Agent created successfully.');
    }



}
