<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'command_name',
        'run_time',
        'is_active',
    ];
}
