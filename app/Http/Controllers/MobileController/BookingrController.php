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
                $date =  date('y-m-d',$nextday);

            }else {
                $date = date("y-m-d", $nextday);
            }

            if(count(Booking::where('customer_id',$req->customer_id)->where('service_id',$sq->service_id)
            ->whereIn('status',[0,1])->get())<3){//customer can book only 3 valid booking in the same service only
                if(count(Booking::where('customer_id',$req->customer_id)->where('service_id',$sq->service_id)
                ->whereIn('status',[0,1])->where('date',$date)->get())==0){
                        $booking = Booking::create([
                            'customer_id' => $req->customer_id,
                            'appointment_id' => $req->appointment_id,
                            'status' => '0',
                            'queue_id' => $time->source_id,
                            'service_id' => $sq->service_id,
                            'date' => $date,
                        ]);

                        $appointment->update(['status'=>1]);
                        return response()->json([ 'booking' => $booking,]);
                    }else{return  response()->json(['message'=>'sorry,you can not book a anew appointment because you have book in the same date ']);

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






    function getTotalCustomer($service_id)
    {
        $sq = ServiceQueue::selectRaw('queue_id')->where('service_id', $service_id)->get();

        if (count($sq) > 0) {
            $min_count=0;
            $min_queue;
            $lock = 1;

            foreach($sq as $obj) {
                $booking = Booking::where('queue_id', $obj->queue_id)->where('date',date('Y-m-d'))->where('status', 0)->get();


                if (count($booking) > 0) {

                    if ($lock) {
                        $min_count = count($booking);
                        $queue_id = $obj->queue_id;
                        $lock = 0;
                    }
                    if (count($booking) <= $min_count) {
                        $min_count = count($booking);
                        $min_queue = $obj->queue_id;
                    }
                }
            }
            return response()->json(['total customer'=>$min_count,
                                        'queue_id'=>$min_queue   ] );
        }else   return response()->json(['message'=>'operation failed']);

    }




    public function getAllAvailableAppointment($service_id,$day)
    {
                    $counter=1;
        $queue= ServiceQueue::where('service_id', $service_id)->get();

        //$q_id = array();

        foreach ($queue as $q) {
            $queue = Queue::where('id', $q->queue_id)
                ->where('active', 1)
                ->first();
                if($queue){
        $time = Time::where('source_id', $q->queue_id)
            ->where('type',2)
            ->where('day', $day)
            ->where('status',1)
            ->first();

        $appointment[$counter++]= Appointment::where('time_id', $time->id)->where('status',0)->orderBy('start_time', 'asc')->get();//return all appointment in the queue


        }}
            return  $appointment;



// $remaped = array();
// foreach ($appointment as $row) {
//     $remaped[$row["0"]['start_time']] = $row;
// }


        }

















public function getExpectedWaitingTime($queue_id)
    {
        $service = ServiceQueue::where('queue_id', $queue_id)
            ->selectRaw('service_id')
            ->first();

        $durationTime = Service::where('id', $service->service_id)
            ->selectRaw('duration_time')
            ->first();

        $delayTime = Booking::where('queue_id', $queue_id)
            ->selectRaw('delay_time')
            ->first();
        if ($delayTime) {

            $waitingTime = ($durationTime * $this->getTotalCustomer($service->service_id)) + $delayTime;

        } else {
            $waitingTime = ($durationTime * $this->getTotalCustomer($service->service_id));
        }
        return response()->json(['The Expected Waiting Time :' => $waitingTime]);

    }














}





 //status=0 mean the confirmed booking status =1 mean the turned booking status=2 canceled booking status=3 mean checkedout boooking























//  $queue_id=DB::table('appointments')
//                 ->join('times', 'appointments.time_id', '=', 'times.id')
// //                 ->where('appointments.id',$req->appointment_id)->get(['source_id']);
// if (count($sq) > 0) {
//     $min_count=0;
//     $min_queue;
//     $lock = 1;

//     foreach($sq as $obj) {
//         $booking = Booking::where('queue_id', $obj->queue_id)->where('date',date('Y-m-d'))->where('status', 0)->get();


//         if (count($booking) > 0) {

//             if ($lock) {
//                 $min_count = count($booking);
//                 $queue_id = $obj->queue_id;
//                 $lock = 0;
//             }
//             if (count($booking) <= $min_count) {
//                 $min_count = count($booking);
//                 $min_queue = $obj->queue_id;
//             }
//         }
//     }
//     return response()->json(['total customer'=>$min_count,
//                                 'queue_id'=>$min_queue   ] );
// }else   return response()->json(['message'=>'operation failed']);
// for($i=1;$i<count($appointment);$i++){


//     if (count($appointment) > 0) {
//         $t1 = Time::where('id', $appointment[$i][]->time_id)->where('status', 1)->first();
//         return $t1;
//         $q1= Queue::where('id', $t1->source_id)->where('active', 1)->first();
//         $min_count=0;
//         $trueAppointment=$appointment[$i];
//         if($q1){
//         $booking = Booking::where('queue_id',$q1->queue_id)->where('day',$day)->where('status', 0)->get();
//               if(count($booking)>0){
//                         $min_count = count($booking);
//                         $min_queue_id = $q1->queue_id;
//               }else{ $min_count=0;
//                             $min_queue = $q1; }



//     }else{
// return "aaa";

//     }}

// else{
//      return response()->json(['message'=>'operation failed']);
// }
//     for($j=$i+1;$i<count($appointment);$j++){


//     if(($appointment[$i]->start_time==$appointment[$j]->start_time)&&($appointment[$i]->end_time==$appointment[$j]->end_time)){
//         $t2 = Time::where('id', $appointment[$j]->time_id)->where('status', 1)->first();
//         $q2= Queue::where('id', $t1->source_id)->where('active', 1)->first();
//         if($q2){
//             $booking = Booking::where('queue_id',$q2->queue_id)->where('day',$day)->where('status', 0)->get();
//                     if(count($booking)<$min_count)  {
//                     $min_count=count($booking);
//                     $trueAppointment=$appointment[$j];
//                     }



//     }
//             }

//         }
//         $array[]=$trueAppointment;









// return  collect($appointment)->sortBy('start_time','ASC');
//         }
