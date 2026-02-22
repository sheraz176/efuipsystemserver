<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\CommandSchedule;
use Carbon\Carbon;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {




          $command = CommandSchedule::where('command_name','2nd:loop')->first();

if ($command && $command->is_active == 1) {

    $time = !empty($command->run_time)
        ? Carbon::parse($command->run_time)->format('H:i')
        : '17:00';

    $schedule->command('2nd:loop')
        ->dailyAt($time)
        ->timezone('Asia/Karachi')
        ->withoutOverlapping(1440);
}


    $command = CommandSchedule::where('command_name','3rd:loop')->first();
if ($command && $command->is_active == 1) {

    $time = !empty($command->run_time)
        ? Carbon::parse($command->run_time)->format('H:i')
        : '20:00';

    $schedule->command('3rd:loop')
        ->dailyAt($time)
        ->timezone('Asia/Karachi')
        ->withoutOverlapping(1440);
}




        $schedule->command('recusive:parallel')
           ->dailyAt('00:30')           
           ->timezone('Asia/Karachi')
         ->withoutOverlapping(1440);  // Prevent 24 hours overlap


          $schedule->command('recusive:parallel')
           ->dailyAt('06:00')          // din ke 06:00 AM
          ->timezone('Asia/Karachi')
           ->withoutOverlapping(1440); // 24 hours overlap prevent

        
    

        $schedule->command('daily:hourly-summary-company')
       ->hourly()                    // every hour
       ->timezone('Asia/Karachi')
       ->withoutOverlapping();       // prevent overlap


 
        $schedule->command('send:sms')
   	 ->everyFiveMinutes()                 // run frequently
    	->withoutOverlapping(10)            // lock expires in 10 minutes
    	->appendOutputTo(storage_path('logs/SMS.log'));

           $schedule->command('stats:update-monthly')
   	   ->everyFiveMinutes()                 // run frequently
    	  ->withoutOverlapping(10)            // lock expires in 10 minutes
    	  ->appendOutputTo(storage_path('logs/SMS.log'));

       
                $schedule->command('recusive:count')
         ->everyThirtyMinutes()         // run every 30 minutes
         ->withoutOverlapping(10)      // lock expires in 10 minutes
         ->appendOutputTo(storage_path('logs/SMS.log'));

          $schedule->command('reminder:sms')
         ->dailyAt('13:22') // Har din 1:22 PM
         ->withoutOverlapping(10) 
         ->appendOutputTo(storage_path('logs/SMS.log'));

         $schedule->command('recusive:count')
         ->dailyAt('00:01')   // Daily at 12:01 AM
        ->withoutOverlapping(10)
        ->appendOutputTo(storage_path('logs/SMS.log'));

         $schedule->command('update:rr')
    ->dailyAt('23:45') // Runs daily at 11:45 PM
    ->withoutOverlapping(10)
    ->appendOutputTo(storage_path('logs/SMS.log'));

   
      

          
         $schedule->command('agent-sales:reset')->dailyAt('00:30');

                
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */

protected function scheduleTimezone()
{
    return 'Asia/Karachi'; // Pakistan timezone
}

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
