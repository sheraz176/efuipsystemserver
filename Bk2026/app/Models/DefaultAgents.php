<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultAgents extends Model
{
    use HasFactory;
         protected $table= 'default_agents';

    protected $fillable = ['agent_name', 'agent_id'];

}
