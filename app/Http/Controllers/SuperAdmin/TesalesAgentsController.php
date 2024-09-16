<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeleSalesAgent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

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

     public function Agentlogout($id)
     {
         $telesalesAgent = TelesalesAgent::findOrFail($id);
        //  dd($telesalesAgent); // This will dump and die, so the rest of the code won't run

         $telesalesAgent->islogin = "0";
         $telesalesAgent->today_logout_time = now();
         $telesalesAgent->update();

         return redirect()->route('telesales-agents.index')->with('success', 'Telesales Agent Logout Successfully.');
     }


     public function InActive($id)
     {
         $telesalesAgent = TelesalesAgent::findOrFail($id);
        //  dd($telesalesAgent); // This will dump and die, so the rest of the code won't run
        $telesalesAgent->islogin = "0";
         $telesalesAgent->status = "0";
         $telesalesAgent->today_logout_time = now();
         $telesalesAgent->update();

         return redirect()->route('telesales-agents.index')->with('success', 'Telesales Agent In Active Successfully.');
     }

     public function AgentData(Request $request)
     {
        //   dd('hi');
         if ($request->ajax()) {
             $data = TelesalesAgent::select('*');
             return Datatables::of($data)
             ->addColumn('action', function($data){

                return '
                <a href="' .route('telesales-agents.edit',$data->agent_id). '" class="btn-all mr-2">
                 <button type="button" class="btn btn-primary btn-sm">Edit</button>
              </a>
               <a href="' .route('superadmin.telesales-agents-emp.edit',$data->agent_id). '" class="btn-all mr-2">
                 <button type="button" class="btn btn-primary btn-sm">Update Emp</button>
               </a>
                 <a href="' .route('superadmin.telesales-agents-logout.edit',$data->agent_id). '" class="btn-all mr-2">
                 <button type="button" class="btn btn-danger btn-sm">LogOut Agent</button>
               </a>

                 <a href="' .route('superadmin.telesales-agents-Inactive.edit',$data->agent_id). '" class="btn-all mr-2">
                 <button type="button" class="btn btn-warning btn-sm">In-Active Agent</button>
                 </a>

                ';

         })

         ->addColumn('status', function ($data) {
            if ($data->status == "1") {
                return '<button type="button" class="btn btn-success btn-sm">Active</button>';
            }
            else{
                return '<button type="button" class="btn btn-danger btn-sm">In Active</button>';
            }
        })
        ->addColumn('islogin', function ($data) {
            if ($data->islogin == "1") {
                return '<button type="button" class="btn btn-success btn-sm">Log In</button>';
            }
            else{
                return '<button type="button" class="btn btn-danger btn-sm">Log Out</button>';
            }
        })

         ->rawColumns(['action','status','islogin'])
                     ->make(true);
         }

     }


}
