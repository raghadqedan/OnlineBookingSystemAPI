<?php

namespace App\Http\Controllers\MobileController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\ServiceQueue;
use App\Models\Booking;
use App\Models\Time;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use App\Models\Queue;
class BookingrController extends Controller
{

    public function createBooking(Request $req)
    {

        $appointment = Appointment::where('id', $req->appointment_id)->where('status', 0)->first();

        if ($appointment) {

            // this appointment is available
            $time = Time::where('id', $appointment->time_id)->where('status', 1)->first();
            $sq = ServiceQueue::where('queue_id', $time->source_id)->first();

            $appointmentdayname = jddayofweek($time->day, 1);
            $nextday = strtotime('next ' . $appointmentdayname . '');
            $weekNoNextDay = date('W', $nextday);
            $weekNo = date('W');
            if ($weekNoNextDay != $weekNo) {
                //customer want to book  appointment for this day
                $date = date("y-m-d"); //currentdate

            }else {
                $date = date("y-m-d", $nextday);
            }

            if(count(Booking::where('customer_id',$req->customer_id)->where('service_id',$sq->service_id)
            ->whereIn('status',[0,1])->get())<=3){//customer can book only 3 valid booking in the same service only
                if(count(Booking::where('customer_id',$req->customer_id)->where('service_id',$sq->service_id)
                ->whereIn('status',[0,1])->where('date',$date)->get())==0){
                        $booking = Booking::create([
                            'customer_id' => $req->customer_id,
                            'appointment_id' => $req->appointment_id,
                            'status' => '1',
                            'queue_id' => $time->source_id,
                            'service_id' => $sq->service_id,
                            'date' => $date,
                        ]);
                        return response()->json([ 'booking' => $booking,]);
                    }else {return  response()->json(['message'=>'sorry,you can not book a anew appointment because you have book in the same date ']);

                    }
                }else {return  response()->json(['message'=>'sorry,you can not book a anew appointment because you have  three valid booking']);}

        }return response()->json(['message' => 'operation failed',]);
    }

    // {
    //     "appointment_id":"168",
    //      "customer_id":"1"
    // }

    public function getAllBooking($customer_id)
    {
        //   get all booking to the customer

        $booking = Booking::where('customer_id', $customer_id)->get();
        return response()->json(['booking' => $booking]);

    }

    public function getBooking($id)
    {
        $booking = Booking::where('id', $id)->get();
        return response()->json(['booking' => $booking]);

    }

/// monitor screen , notification api



}


























 // $queue_id=DB::table('appointments')
            //     ->join('times', 'appointments.time_id', '=', 'times.id')
            //     ->where('appointments.id',$req->appointment_id)->get(['source_id']);
