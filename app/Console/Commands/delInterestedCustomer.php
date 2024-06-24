<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InterestedCustomers\InterestedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class delInterestedCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'del:interstadCustomer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::enableQueryLog();
        $InterestedCustomers = InterestedCustomer::where('company_id', 12)
    ->where('deduction_applied', 0)
    ->whereBetween('created_at', ['2024-06-01', '2024-06-24'])
    ->get();
        // dd(DB::getQueryLog());
            dd($InterestedCustomers);


        foreach($InterestedCustomers  as $InterestedCustomer){

          $find_ref = InterestedCustomer::where('id',$InterestedCustomer->id)->get();
         $find_ref->each->delete();



        }

        return 'success';
        return 0;

    }
}
