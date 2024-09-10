<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentCount extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table= 'agent_counts';
    protected $fillable = ['id',
                            'count',
                            'company_id',

                        ];
}
