<?php

namespace App\Http\Controllers\SuperAgentInterested;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAgentDashboardControllerInterested extends Controller
{
    public function index()
    {
        return view('super_agent_Interested.dashboard');
    }
}
