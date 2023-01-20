<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\ServiceQueue;
use App\Models\Appointment;
use App\Models\Queue;
use App\Models\Time;
use DB;
class ControlQueues extends Controller
{
    public function getCurrentQueue(){

        $queues= Queue::where('user_id',auth()->user()->id)->where('active',1)->get();

        foreach($queues as $q){
                $time=Time::where('source_id',$q->id)->where('type',2)->where('status',1)->where('day', date('N', strtotime(date('l')))-1)->where('start_time','<=',date('H:i:s'))->where('end_time','>=',date('H:i:s'))->first();
                if($time){
                    return response()->json(['active_queue_id'=>$time->source_id ]);
                }
        }
                return  response()->json(['message'=>"operation failed" ]);

    }

    public function getCurrentCustomer($queue_id){

            $company_type=Company::selectRaw('type')->where('id',auth()->user()->company_id)->where('status',1)->first();
                if( $company_type->type==0){//if companytype is number
                //return the turned customer id to this queue if exist

                        $currentCustomerId=Booking::selectRaw('customer_id')->where('date',date('Y-m-d'))->where('queue_id',$queue_id)->where('status',0)->where('priority',100)->orderBy('number', 'asc')->first();
                        if($currentCustomerId){
                                $customer= Customer::where('id',$currentCustomerId->customer_id)->first();
                                return response()->json(['customer'=>$customer ]);
                        }else{
                            $currentCustomerId=Booking::selectRaw('customer_id')->where('queue_id',$queue_id)->where('date',date('Y-m-d'))->where('status',0)->orderBy('number', 'asc')->first();
                            if($currentCustomerId){
                                    $customer= Customer::where('id',$currentCustomerId->customer_id)->first();
                                    return response()->json(['customer'=>$customer ]);}
                            }
                                return  response()->json(['message'=>"operation failed" ]);
                    }else{
                            //if company type is time
                            $allAppointment_id=Booking::selectRaw('appointment_id')->where('queue_id',$queue_id)->where('date',date('Y-m-d'))->where('status',0)->where('priority',100)->get();
                            if(count($allAppointment_id)){
                                        $min_start_time_appointment_id;
                                        $lock=1;
                                        foreach($allAppointment_id as $obj){
                                                $appointment=Appointment::where('id',$obj->appointment_id)->where('status',1)->first();
                                                if($appointment){
                                                        if($lock){
                                                            $min_start_time_appointment=$appointment;
                                                            $lock=0; }

                                                        if($appointment->start_time<=$min_start_time_appointment->start_time){
                                                                    $min_start_time_appointment_id=$appointment->id;}

                                                }else {return  response()->json(['message'=>"operation failed" ]);}
                                            }
                                        $customer_id=Booking::selectRaw('customer_id')->where('appointment_id',$min_start_time_appointment->id)->where('date',date('Y-m-d'))->first();
                                        return response()->json(['customer'=>$customer=Customer::where('id',$customer_id->customer_id)->first()]);

                            }
                            else{
                                $allAppointment_id=Booking::selectRaw('appointment_id')->where('queue_id',$queue_id)->where('date',date('Y-m-d'))->where('status',0)->get();
                                if(count($allAppointment_id)){
                                        $min_start_time_appointment;
                                        $lock=1;
                                        foreach($allAppointment_id as $obj){
                                                    $appointment=Appointment::where('id',$obj->appointment_id)->where('status',1)->first();
                                                    if($appointment){
                                                            if($lock){
                                                                    $min_start_time_appointment=$appointment;
                                                                    $lock=0;
                                                                }
                                                            if($appointment->start_time<=$min_start_time_appointment->start_time&&$appointment->start_time<= date("h:i:sa")){
                                                                $min_start_time_appointment_id=$appointment;
                                                                }


                                                    }else return   response()->json(['message'=>"operation failed" ]);

                                            }
                                        $customer_id=Booking::selectRaw('customer_id')->where('appointment_id',$min_start_time_appointment->id)->where('date',date('Y-m-d'))->first();
                                        return response()->json(['customer'=>$customer=Customer::where('id',$customer_id->customer_id)->first()]);
                                    }
                                }



            }
    }






    function turnCustomer($booking_id,$destination_service_id){

            $b=Booking::where('id',$booking_id)->first();
            $sq=ServiceQueue::selectRaw('queue_id')->where('service_id',$destination_service_id)->get();

            if(count($sq)){
                                $min_count;
                                $min_queue;
                                $lock=1;

                        foreach($sq as $obj){
                                $booking=Booking::where('queue_id',$obj->queue_id)->where('date',date('Y-m-d'))->where('status',0)->get();

                                if($booking){
                                        if($lock){
                                                $min_count=count($booking);
                                                $queue_id=$obj->queue_id;
                                                $lock=0;
                                        }
                                    if(count($booking)<=$min_count){
                                        $min_count=count($booking);
                                        $min_queue=$obj->queue_id;
                                    }


                                }

                        }

                        $result=Booking::create([
                            'service_id'=>$destination_service_id,
                            'queue_id'=>$min_queue,
                            'customer_id'=>$b->customer_id,
                            'status'=>"0",
                            'priority'=>"100",
                            'date'=>date('y-m-d'),
                            'appointment_id'=>$b->appointment_id,


                        ]);
                    if($result){
                            $b=Booking::where('id',$booking_id)->first();
                            $b->update(['status'=>1]);
                            return  response()->json(['message'=>"Turned customer successfully" ]);

                    }
        }

            return  response()->json(['message'=>"operation failed" ]);

    }









    function CheckOut($booking_id){
            //when check out customer set appointment atatus=0,booking status=2
            $booking=Booking::where('id',$booking_id)->where('status',0)->first();

            if($booking){
                $company_type=Company::selectRaw('type')->where('id',auth()->user()->company_id)->where('status',1)->first();
                if( $company_type->type==1){
                $appointment=Appointment::where('id',$booking->appointment_id)->where('status',1)->first();
                $appointment->update(['status'=>0]);}

                $b=Booking::where('customer_id',$booking->customer_id)->where('appointment_id',$booking->appointment_id)->whereIn('status',[0,1])->where('date',$booking->date)->get();
                foreach($b as $s){
                    $s->update(['status'=>3]);
                    }
                }else{
                    return  response()->json(['message'=>"operation failed" ]);
                }

                    return  response()->json(['message'=>"Checkout  customer successfully" ]);

            }

    function takeExtraTime($booking_id,$delay_Time){

        $booking=Booking::where('id',$booking_id)->where('status',0)->first();

        if($booking){

                $booking->update(['delay_time'=>$delay_Time]);
                return  response()->json(['message'=>"customer take extra time  successfully" ]);

            }
                return  response()->json(['message'=>"operation failed" ]);

        }







        function getTotalCustomer($service_id)
        {
            $sq = ServiceQueue::selectRaw('queue_id')->where('service_id', $service_id)->get();

            if (count($sq) > 0) {
                $min_count;
                $min_queue;
                $lock = 1;

                foreach($sq as $obj) {
                    $booking = Booking::where('queue_id', $obj->queue_id)->where('date',date('Y-m-d'))->where('status', 0)->get();


                    if (count($booking) > 0) {
                       // return 's';
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
            }
            return response()->json(['total customer', $min_count]);
        }

















}









//note in get appointment get the allappointment with start_time >current_time








