<?php

namespace App\Http\Controllers\SuperAgentL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAgentDashboardLController extends Controller
{
    public function index()
    {
        return view('super_agent_l.dashboard');
    }
}
