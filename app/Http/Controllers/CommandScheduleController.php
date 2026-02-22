<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommandSchedule;
use Illuminate\Support\Facades\Artisan;

class CommandScheduleController extends Controller
{
    // Show form
    public function index()
{
    $commands = CommandSchedule::whereIn('command_name', [
        '2nd:loop',
        '3rd:loop'
    ])->get()->keyBy('command_name');

    // Agar record exist nahi karta to create kar dein
    if (!isset($commands['2nd:loop'])) {
        $commands['2nd:loop'] = CommandSchedule::create([
            'command_name' => '2nd:loop',
            'run_time' => '17:00',
            'is_active' => 1
        ]);
    }

    if (!isset($commands['3rd:loop'])) {
        $commands['3rd:loop'] = CommandSchedule::create([
            'command_name' => '3rd:loop',
            'run_time' => '17:00',
            'is_active' => 1
        ]);
    }

    return view('superadmin.command_schedule.index', compact('commands'));
}
    // Update command settings
   

public function update(Request $request)
{
    $request->validate([
        'run_time' => 'required|date_format:H:i',
        'is_active' => 'required|boolean',
        'command_name' => 'required|string',
    ]);

    CommandSchedule::updateOrCreate(
        ['command_name' => $request->command_name], // dynamic
        [
            'run_time'  => $request->run_time,
            'is_active' => $request->is_active
        ]
    );

    return back()->with('success', 'Command Updated Successfully');
}

    // Optional: Run immediately
    public function runNow()
    {
        Artisan::call('2nd:loop');
        return back()->with('success', 'Command Run Successfully');
    }
}
