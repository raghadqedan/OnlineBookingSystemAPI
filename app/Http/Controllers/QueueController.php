<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Company;
use App\Models\User;
use App\Models\Time;
use App\Models\Appointment;
use App\Models\Booking;
use Auth;
use DB;
use App\Http\Controllers\TimeController;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\TrustProxies as MiddleWare;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ServiceQueueController;
use App\Jobs\ActiveQueue;



class QueueController extends Controller
{

    function getDetails($id)
    {
    $queue=Queue::find($id);

        return$queue;
    }


    function getAllQueue()
    {
            $queues=DB::table('users')
                ->join('queues', 'users.id', '=', 'queues.user_id')
                ->where('company_id',auth()->user()->company_id)->get(['queues.id','queues.name']);
                return response()->json(['queues'=>$queues]);
    }




    public function addQueue(request $req)
    {
            $validator=Validator::make($req->all(),[
            'services' =>'required',
            'name' =>'required',
            'start_regesteration' =>'required',
            'repeats' =>'required',
            'user_id'=>'required',
            ]);

        if($validator->fails()){
                return response()->json([
                    'validation_error'=>$validator->messages(),
            ]);}

            else{

                $company_type=CompanyController::getCompanyType();

                  //timeQueue
                if($company_type&&(count($req->services)>1))
                    return response()->json(['message'=>'select only one service Becaus your company system type is time']);
                    else{
                        $queue=Queue::create([
                            'name'=>$req->name,
                            'start_regesteration'=>$req->start_regesteration,
                            'repeats'=>$req->repeats,
                            'user_id'=>$req->user_id,
                        ]);

                        $id=$queue->id;
                        ServiceQueueController::createServiceQueue($id,$req->services);

                      //create  queues default scheduleTimes for the queue wuth  user start,end times  value.
                        for($i=0;$i<7;$i++){

                            $obj=Time::where('source_id',$queue->user_id)->where('day',$i)->where('type',"1")->first();
                            if($obj->status=="1"){
                            $request = new Request([
                                'day'=>$i,
                                'type'=>"2",
                                'source_id'=>$queue->id,
                                'start_time'=> $obj->start_time,
                                'end_time'=> $obj->end_time,
                                'status'=>"1"
                            ]);}
                            else{
                                $request = new Request([
                                    'day'=>$i,
                                    'type'=>"2",
                                    'source_id'=>$queue->id,
                                    'start_time'=> $obj->start_time,
                                    'end_time'=> $obj->end_time,
                                    'status'=>"0"
                                ]);
                            }
                            $result=TimeController::createTime( $request);
                            if($result=="0"){
                                return  response()->json([
                                    "result"=>"Operation faild",]
                                );
                            }
                        }

                            return  response()->json([
                                "result"=>"Queue created successfully",
                                "queue"=>$queue,
                                "services"=>ServiceQueueController::getService($queue->id),
                        ]);



                    }


        }
    }


    //   {
    //     "services":[1,2],
    //     "name":"Q1",
    //     "start_regesteration":"2022-12-06 16:00:39",
    //     "repeats":"1",
    //     "user_id":"7"
    //     }



    function updateDetails(Request $req, $id)
    {
            $queue= Queue::find($id);
            $queue->update([
                'name'=>$req->name,
                'start_regesteration'=>$req->start_regesteration,
                'repeats'=>$req->repeats,
                'user_id'=>$req->user_id,
            ]);

            $id=$queue->id;
            ServiceQueueController::updateService($id,$req->services);

            return  response()->json([
                "result"=>"Queue updated successfully",
                "queue"=>$queue,
                "services"=>ServiceQueueController::getService($queue->id),]);
    }






    static  public function delete($id)
    {
            $r1=Queue::where('id', $id)->delete();
            if ($r1) {
                return ["result"=>"Queue deleted"];
            } else {
                return ["result"=>"Operation faild"];
            }
    }
//errors
    function deleteQueue($queue_id){

        $queue= Queue::where('id',$queue_id)->first();
        if($queue){
            $obj=Time::where('source_id',$queue_id)->where('type',2)->whereIn('status', [0,1])->get();//return all children times for this queue

            if($obj){
                    foreach($obj as $time){//$time->id
                            $appointments=Appointment::where('time_id',55)->whereIn('status', [0,1,10])->get();

                            if($appointments){

                                foreach($appointments as $appointment){

                                    $booking=Booking::where('appointment_id',$appointment->id)->whereIn('status', [0,1,10])->get();

                                    if($booking){
                                            foreach ($booking as $book){
                                                if($book->status==1 ||$book->status==0){
                                                    //TODO:send email "your booking cancelled please book anew book in another time"//because company put this day as off day
                                                   //email   "your booking cancelled please book anew book in another time";
                                                }
                                                $book->update(['status'=>-1],);
                                                }

                                            }
                                            $appointment->update(['status'=>-1]);//make the appointment is deleted
                                }
                            }

                        }

                }
        $obj->update(['status'=>-1]);
        $queue->update(['status'=>-1]);
        return  response()->json([ 'message'=>'Queue deleted successfully' ]);

        }else{
            return  response()->json([ 'message'=>'opration failed ' ]);
        }
    }




















}
