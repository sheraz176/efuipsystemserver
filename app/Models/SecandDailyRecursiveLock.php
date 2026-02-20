<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecandDailyRecursiveLock extends Model
{
    use HasFactory;

     protected $table = 'secand_daily_recusive_locks';
     protected $fillable = ['subscription_id', 'process_date'];
}
