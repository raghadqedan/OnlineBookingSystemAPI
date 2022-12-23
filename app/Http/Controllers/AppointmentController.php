<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Queue;
use App\Models\ServiceQueue;
use App\Models\Time;
use Illuminate\Http\Response;


class AppointmentController extends Controller
{
            static function createAppointment($time_id){
                $time=Time::where('id',$time_id)->first();
                $service_id=ServiceQueue::selectRaw('service_id')->where('queue_id',$time->source_id)->first();
                $duration_time="00:00:15";//error in $service_id because it is null
                //Service::selectRaw('duration_time')->where('id',$service_id)->first();
            // $repeats=Queue::selectRaw('repeats')->where('id',$time->source_id)->first();
            // return $repeats;
        //while($current_date<=date("Y-m-d", strtotime('+'.$repeats.'week', strtotime($active_date))))
            //  todo   $active_time=queue active time,then add date column  date = date("Y-m-d", strtotime('+'.$day.'day', strtotime($active_date))) to apply active date idea
            // create appoitment operation can applly if currentdate<date("Y-m-d", strtotime('+'.$repeats.'week', strtotime($active_date))) to apply repeats idea

            $start_time=$time->start_time;
            $end_time=$time->end_time;
                $bool=1;
                if($start_time<$end_time){
                        $end_time="00:00:00";
                while($bool&& (date("H:i:s",strtotime($end_time)+strtotime($duration_time))<=$time->end_time)){

                    $secs = strtotime($start_time)+strtotime($duration_time);
                    $end_time = date("H:i:s",$secs);

                $obj=Appointment::create([
                    'start_time'=>$start_time,
                    'end_time'=>$end_time,
                    'status'=>"1",
                    'time_id'=>$time->id,

                ]);

                if(!$obj){
                    return 0;
                    }
                if($start_time==$end_time){
                $bool=0;
                }
                $start_time=$end_time;


        } return 1;
            }
            else{
            return  0;
            }


        }



                //$appoitments=Appoitments::selectRow(('time_id',$time_id),('start_time'< $start_time)||('end_time'> $end_time));
                //check if this appoitment is booked then send notification to the customer "your booking cancel"  then delete this appoitment
                //else only delete it . for serch($appoitmnt_id) this method must but in booking table.




            static function updateAppoitment($time_id,$queue_id,$start_time,$end_time){

            $service_id=ServiceQueueController::getService($queue_id);
            $duration_time=ServiceController::getServiceDurationTime($service_id);
            while($start_time!=$end_time){
                //continue
                $obj= new Appointment();
                $obj=$time_id;//error without testing
                $obj->start_time=$start_time;
                $obj->end_time=$start_time+$duration_time;
                $start_time=$start_time+$duration_time;
                $obj->save();
                }


            }





        //TODO: get appoitment function by day and company id
        //Todo,get them in We arrange them in ascending order start_time , and if there is a similarity between them, we return the date of the least-roomed q






















}



// static function createAppointment($time_id){
//     $time=Time::where('id',$time_id)->first();
//     $service_id=ServiceQueue::selectRaw('service_id')->where('queue_id',$time->source_id)->first();
//     $duration_time="00:00:15";//error in $service_id because it is null
//     //Service::selectRaw('duration_time')->where('id',$service_id)->first();
//    // $repeats=Queue::selectRaw('repeats')->where('id',$time->source_id)->first();
//    // return $repeats;
// //while($current_date<=date("Y-m-d", strtotime('+'.$repeats.'week', strtotime($active_date))))
// //  todo   $active_time=queue active time,then add date column  date = date("Y-m-d", strtotime('+'.$day.'day', strtotime($active_date))) to apply active date idea
// // create appoitment operation can applly if currentdate<date("Y-m-d", strtotime('+'.$repeats.'week', strtotime($active_date))) to apply repeats idea

// $start_time=$time->start_time;
// $end_time=$time->end_time;
//     $bool=1;
//     if($start_time<$end_time){
//             $end_time="00:00:00";
//     while($bool&& (date("H:i:s",strtotime($end_time)+strtotime($duration_time))<=$time->end_time)){

//         $secs = strtotime($start_time)+strtotime($duration_time);
//         $end_time = date("H:i:s",$secs);

//     $obj=Appointment::create([
//         'start_time'=>$start_time,
//         'end_time'=>$end_time,
//         'status'=>"1",
//         'time_id'=>$time->id,

//     ]);

//     if(!$obj){
//         return 0;
//         }
//     if($start_time==$end_time){
//     $bool=0;
//     }
//     $start_time=$end_time;


// } return 1;
// }
// else{
// return  0;
// }


// }
