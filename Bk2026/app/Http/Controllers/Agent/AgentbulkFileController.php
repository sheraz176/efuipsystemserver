<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentbulkFileController extends Controller
{

 public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt',
    ]);

    $path = $request->file('file')->store('bulkfiles');

    return back()->with('success', 'File uploaded successfully.');
}

}
