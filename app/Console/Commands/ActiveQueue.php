<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Queue;
use Illuminate\Support\Carbon;
class ActiveQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Active:Queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Active the   Queue when the current day equal the queue  death  time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $currentDate=date("y-m-d");
        $allInActiveQueue=Queue::where('active',0)->get();
        foreach( $allInActiveQueue as $q){
                $activeDate=Queue::selectRaw('start_regesteration')->where('id',$q->id)->first();
                $date=date('y-m-d',strtotime($activeDate->start_regesteration));
                    if($currentDate==$date){
                            $q->update(['active'=>1]);
                }
        }
    }








    }

