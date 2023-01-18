<?php

namespace App\Http\Controllers;
use DB;
use App\Models\ServiceQueue;
use App\Models\Booking;
use App\Models\Time;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;

use App\Models\Queue;
class BookingController extends Controller{


         function getUpComingBooking(){
            $count=0;
            if(auth()->user()->role_id==1){
                $users= User::where('company_id',auth()->user()->company_id)->get();

                if($users){
                    foreach($users as $u){
                    $q=DB::table('booking')
                    ->join('queues', 'booking.queue_id', '=', 'queues.id')
                    ->where('booking.status',0)
                    ->where('booking.date','>=',date('y-m-d'))
                    ->where('queues.user_id',$u->id)
                    ->get();
                    if(count($q)>0){
                        foreach($q as $w){
                        $queues[++$count]= response()->json([
                            "date"=>(Booking::selectRaw('date')->where('id',$w->date)->first()),
                            "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                            "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                            "service_time/number"=>(Company::selectRaw('type')->where('id',auth()->user()->company_id)->first())->type?$w->number:(Appointment::where('id',$w->appointment_id))->get(['start_time','end_time']),
                            "user_name"=>(User::selectRaw('name')->where('id',$u->id)->first())->name,
                        ]);
                    }}
                    }return (!empty($queues))?$queues: response()->json(["message"=>"no booking " ]);
                    }else{return  response()->json(["message"=>"no booking " ]);}

                }else{
                $queue=DB::table('booking')
                            ->join('queues', 'booking.queue_id', '=', 'queues.id')
                            ->whereIn('booking.status',[0])
                            ->where('booking.date','>=',date('y-m-d'))
                            ->where('queues.user_id',auth()->user()->id)
                            ->get();


                            if(count($queue)>0){
                                foreach($queue as $w){
                                $queues[++$count]= response()->json([
                                    "date"=>(Booking::selectRaw('date')->where('id',$w->date)->first())->date,
                                    "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                                    "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                                    "service_time/number"=>(Company::selectRaw('type')->where('id',auth()->user()->company_id))?$w->number:Appointment::selectRaw('start_time','end_time')->where('id',$w->appointment_id)->get(),

                                ]);
                            }
                            return (!empty($queues))?$queues: response()->json(["message"=>"no booking " ]);}
                            return  response()->json(["message"=>"no booking " ]);}
                            }

                }




                function expiredBooking(){
                    $count=0;
                    if(auth()->user()->role_id==1){
                        $users= User::where('company_id',auth()->user()->company_id)->get();
                        if($users){
                            foreach($users as $u){
                            $q=DB::table('booking')
                            ->join('queues', 'booking.queue_id', '=', 'queues.id')
                            ->where('booking.status',2)
                            ->where('booking.date','<',date('y-m-d'))
                            ->where('queues.user_id',$u->id)
                            ->get();
                            if(count($q)>0){
                                foreach($q as $w){
                                $queues[++$count]= response()->json([
                                    "date"=>(Booking::selectRaw('date')->where('id',$w->date)->first())->date,
                                    "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                                    "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                                    "service_time/number"=>(Company::selectRaw('type')->where('id',auth()->user()->company_id))?$w->number:Appointment::selectRaw('start_time','end_time')->where('id',$w->appointment_id)->get(),
                                    "user_name"=>(User::selectRaw('name')->where('id',$u->id)->first())->name,
                                ]);
                            }
                            return json_decode($this->setQueueOffDay( $queues)->getContent(), true);}
                            }
                            }else{return  response()->json(["message"=>"No booking yet" ]);}

                        }else{
                        $queue=DB::table('booking')
                                    ->join('queues', 'booking.queue_id', '=', 'queues.id')
                                    ->where('booking.status',2)
                                    ->where('booking.date','<',date('y-m-d')) //todo::compare time
                                    ->where('queues.user_id',auth()->user()->id)
                                    ->get();

                                    if(count($queue)>0){
                                        foreach($queue as $w){
                                        $queues[++$count]= response()->json([
                                            "date"=>(Booking::selectRaw('date')->where('id',$w->date)->first())->date,
                                            "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                                            "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                                            "service_time/number"=>(Company::selectRaw('type')->where('id',auth()->user()->company_id))?$w->number:Appointment::selectRaw('start_time','end_time')->where('id',$w->appointment_id)->get(),

                                        ]);
                                    }
                                    return $queues;}
                                    }

                        }























