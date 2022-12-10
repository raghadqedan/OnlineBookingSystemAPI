<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ServiceQueueControllerController;
use App\Http\Controllers\ServiceControllerController;
use Illuminate\Http\Request;
use App\Models\Appoitment;

class AppoitmentController extends Controller
{
    static function createAppoitment($time_id,$queue_id,$start_time,$end_time){

        $service_id=ServiceQueueController::getService($queue_id);
        $duration_time=ServiceController::getServiceDurationTime($service_id);
        //$appoitments=Appoitments::selectRow(('time_id',$time_id),('start_time'< $start_time)||('end_time'> $end_time));
        //check if this appoitment is booked then send notification to the customer "your booking cancel"  then delete this appoitment 
        //else only delete it . for serch($appoitmnt_id) this method must but in booking table.
        while($start_time!=$end_time){
            //continue
        $obj= new Appoitment();
        $obj=$time_id;//error without testing 
        $obj->start_time=$start_time;
        $obj->end_time=$start_time+$duration_time;
        $start_time=$start_time+$duration_time;
        $obj->save();
 }}
    static function updateAppoitment($time_id,$queue_id,$start_time,$end_time){

    $service_id=ServiceQueueController::getService($queue_id);
    $duration_time=ServiceController::getServiceDurationTime($service_id);
    while($start_time!=$end_time){
        //continue
    $obj= new Appoitment();
    $obj=$time_id;//error without testing 
    $obj->start_time=$start_time;
    $obj->end_time=$start_time+$duration_time;
    $start_time=$start_time+$duration_time;
    $obj->save();
}


    }
}
