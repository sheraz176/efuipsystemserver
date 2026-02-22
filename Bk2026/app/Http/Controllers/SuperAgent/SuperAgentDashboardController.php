<?php

namespace App\Http\Controllers\SuperAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAgentDashboardController extends Controller
{
    public function index()
    {
        return view('super_agent.dashboard');
    }
}
