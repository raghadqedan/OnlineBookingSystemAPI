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
    {

        $currentDate=date("y-m-d");
        $allActiveQueue=Queue::where('active',1)->get();
        if($allActiveQueue){
        foreach($allActiveQueue as $q){
                $repeats=Queue::selectRaw('repeats')->where('id',$q->id)->first();

                if($repeats->repeats!="all days"){

                    $queueDeathDate= date('y-m-d', strtotime('+'.$repeats->repeats.'', strtotime($q->start_regesteration)));
                    return $queueDeathDate;
                    if($currentDate==$queueDeathDate)
                                QueueController::deleteQueue($q->id);
        }}




        }

    }




}
