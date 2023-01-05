<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Queue;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Carbon;

class DeathQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Death:Queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete the   Queue when the current day equal the queue  death  time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {//todo::
        // $q= Queue::where('id',4)->first();
        // $q->update(['active'=>3]);

            $currentDate= Carbon::now()->format('y-m-d');
            $allActiveQueue=Queue::where('active',1)->get();
            foreach($allActiveQueue as $q){
                $repeats=Queue::selectRaw('repeats')->where('id',$q->id)->first();
                $queueDeathDate=new Carbon('Y-m-d', strtotime('+'.$repeats.'week', strtotime($active_date)));
                    if($currentDate==$queueDeathDate)
                    QueueController::deleteQueue($q->id);
            }




            }


}
