<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeleSalesAgent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TesalesAgentsController extends Controller
{
     // Show the form for editing the specified telesales agent.
     public function edit($telesalesAgent)
     {
         $telesalesAgent = TelesalesAgent::findOrFail($telesalesAgent);
         //echo $telesalesAgent;
         return view('superadmin.telesales-agents.editEmp', compact('telesalesAgent'));
     }

     // Update the specified telesales agent in the database.
     public function update(Request $request)
     {

        // dd($request->all());
         $validator = Validator::make($request->all(), [
             'id' => 'required',
             'emp_code' => 'required',
         ]);

         // If validation fails, redirect back with errors
         if ($validator->fails()) {
             return redirect()
                 ->back()
                 ->withErrors($validator)
                 ->withInput();
         }
         $telesaleAgents = TeleSalesAgent::find($request->id);
         $telesaleAgents->emp_code = $request->emp_code;
         $telesaleAgents->update();

         return redirect()->route('telesales-agents.index')->with('success', 'Telesales Agent updated successfully.');
     }

}
