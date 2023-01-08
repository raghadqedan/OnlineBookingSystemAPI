<?php

namespace App\Http\Controllers;
use DB;
use App\Models\ServiceQueue;
use App\Models\Booking;
use App\Models\Time;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Queue;
class BookingController extends Controller{

    function createBooking(Request $req){

            $appointment=Appointment::where('id',$req->appointment_id)->where('status',0)->first();

            if($appointment){

                // this appointment is available
                $time=Time::where('id',$appointment->time_id)->where('status',1)->first();
                $sq=ServiceQueue::where('queue_id',$time->source_id)->first();

                $appointmentdayname =jddayofweek($time->day,1);
                $nextday = strtotime('next '. $appointmentdayname.'');
                $weekNoNextDay = date('W',$nextday);
                $weekNo = date('W');
                if ($weekNoNextDay != $weekNo) {
                    //customer want to book  appointment for this day
                    $date= date("y-m-d");//currentdate

                }else{
                        $date=date("y-m-d",$nextday);
                    }
            // $queue_id=DB::table('appointments')
            //     ->join('times', 'appointments.time_id', '=', 'times.id')
            //     ->where('appointments.id',$req->appointment_id)->get(['source_id']);

            $booking=Booking::create([
                'customer_id'=>$req->customer_id,
                'appointment_id'=>$req->appointment_id,
                'status'=>'1',
                'queue_id'=>$time->source_id,
                'service_id'=>$sq->service_id,
                'date'=>$date
                ]);
            return response()->json([
                'booking'=>$booking

                ]);

        }else{

            return  response()->json([
                'message'=>'operation failed'

            ]);
        }
    }

        // {
        //     "appointment_id":"168",
        //      "customer_id":"1"
        // }



    function getBooking($customer_id){
        $booking = Booking::where('id',$customer_id)->get();
        return response()->json(['booking' => $booking]);

    }



    function g(){
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




    }}





}
