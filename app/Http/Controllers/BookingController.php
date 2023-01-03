<?php

namespace App\Http\Controllers;
use DB;
use App\Models\ServiceQueue;
use App\Models\Booking;
use App\Models\Time;
use App\Models\Appointment;
use Illuminate\Http\Request;

class BookingController extends Controller{
    function createBooking(Request $req){
           //todo::create book and set data of  the selected appoitment day as number  ,
            $appointment=Appointment::where('id',$req->appointment_id)->where('status',0)->first();

            if($appointment){

                // this appointment is available
                $time=Time::where('id',$appointment->time_id)->where('status',1)->first();

                $sq=ServiceQueue::where('queue_id',$time->source_id)->first();

                $appointmentdayname =jddayofweek($time->day,1);
                $nextday = strtotime('next '. $appointmentdayname.'');
                $weekNoNextDay = date('W',$nextday);
                $weekNo = date('W');
                // $daynum = date("w", strtotime( date('l')));//sunday==0 .....
                if ($weekNoNextDay != $weekNo) {
                    //customer want to book  appointment for this day
                    $date= date("y-m-d");//currentdate

                }else{
                        $date=date("y-m-d",$nextday);;
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
            response()->json([
                'message'=>'operation failed'

            ]);
        }}



    function getAllBooking($customer_id){

           //get all booking to the customer


    }

    function getBooking($booking_id){
          //get one booking to the summary page









    }



    function getOnDays($company_id){



    }


    function getAllAvailableAppointment(){




    }


    function getServices($company_id){




    }


    function getcompanyDetails($company_id){

          //description ,address



    }


    function getOnTimes($company_id){

            //get all times from time table  for this source_id where status==1

    }



    function getTotalCustomer($queue_id){
        //get all times from time table  for this source_id where status==1

}


function getExpectedWaitingTime($queue_id){
    //get all times from time table  for this source_id where status==1

}


function takeNumber($queue_id){
    //get all times from time table  for this source_id where status==1

}


/// monitor screen , notification apis
























}
//date = date("Y-m-d", strtotime('+'.$day.'day', strtotime($active_date)))
    //if appointment day <$daynum
    // $numericDay = "1";
    // $newDate = date('l', strtotime("Sunday +{$numericDay} days"));

