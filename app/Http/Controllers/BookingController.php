<?php

namespace App\Http\Controllers;
use DB;
use App\Models\ServiceQueue;
use App\Models\Booking;
use App\Models\Time;
use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
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
                            "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                            "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                            "service_duration_time"=>(Service::selectRaw('duration_time')->where('id',$w->service_id)->first())->duration_time,
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
                                    "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                                    "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                                    "service_duration_time"=>(Service::selectRaw('duration_time')->where('id',$w->service_id)->first())->duration_time,

                                ]);
                            }
                            return (!empty($queues))?$queues: response()->json(["message"=>"no booking " ]);}
                            return  response()->json(["message"=>"no booking " ]);}
                            }

                }




                function getRecentlyBooking(){
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
                                    "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                                    "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                                    "service_duration_time"=>(Service::selectRaw('duration_time')->where('id',$w->service_id)->first())->duration_time,
                                    "user_name"=>(User::selectRaw('name')->where('id',$u->id)->first())->name,
                                ]);
                            }
                            return $queues;}
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
                                            "client_name"=>(Customer::selectRaw('name')->where('id',$w->customer_id)->first())->name,
                                            "service_name"=>(Service::selectRaw('name')->where('id',$w->service_id)->first())->name,
                                            "service_duration_time"=>(Service::selectRaw('duration_time')->where('id',$w->service_id)->first())->duration_time,

                                        ]);
                                    }
                                    return $queues;}
                                    }

                        }























