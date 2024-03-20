<?php

namespace App\Http\Controllers\BasicAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeleSalesAgent;
use App\Models\Company\CompanyProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;




class TeleSalesAgentController extends Controller
{
    public function index()
    {
        $telesalesAgents = TelesalesAgent::all();
        return view('superadmin.telesales-agents.index', compact('telesalesAgents'));
    }

    // Show the form for creating a new telesales agent.
    public function create()
    {
        
        $companies = CompanyProfile::all();
        return view('superadmin.telesales-agents.create',compact('companies'));
    }

    // Store a newly created telesales agent in the database.
    public function store(Request $request)
    {
        //dd($request);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:tele_sales_agents',
            'email' => 'required|email|unique:tele_sales_agents',
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
    
        // Create TelesalesAgent record
        TelesalesAgent::create($validatedData);
    
        return redirect()->route('telesales-agents.index')->with('success', 'Telesales Agent created successfully.');
    }

    // Display the specified telesales agent.
    public function show(TelesalesAgent $telesalesAgent)
    {
        $telesalesAgents = TelesalesAgent::all();
        return view('superadmin.telesales-agents.show', compact('telesalesAgent'));
    }

    // Show the form for editing the specified telesales agent.
    public function edit($telesalesAgent)
    {
        $telesalesAgent = TelesalesAgent::findOrFail($telesalesAgent);
        $companies = CompanyProfile::all();
        //echo $telesalesAgent; 
        return view('superadmin.telesales-agents.edit', compact('telesalesAgent','companies'));
    }

    // Update the specified telesales agent in the database.
    public function update(Request $request, TelesalesAgent $telesalesAgent)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'email' => 'required',
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

        $telesalesAgent->update($validatedData);

        return redirect()->route('telesales-agents.index')->with('success', 'Telesales Agent updated successfully.');
    }

    // Remove the specified telesales agent from the database.
    public function destroy(TelesalesAgent $telesalesAgent)
    {
        $telesalesAgent->delete();

        return redirect()->route('telesales-agents.index')->with('success', 'Telesales Agent deleted successfully.');
    }
}
