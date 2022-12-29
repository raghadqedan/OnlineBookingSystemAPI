<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Company;
use App\Models\User;
use App\Models\Time;
use Auth;
use App\Http\Controllers\TimeController;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\TrustProxies as MiddleWare;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ServiceQueueController;



class QueueController extends Controller
{

    function getDetails($id)
    {
    $queue=Queue::find($id);

        return$queue;
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
                            $request = new Request([
                                'day'=>$i,
                                'type'=>"2",
                                'source_id'=>$queue->id,
                                'start_time'=> $obj->start_time,
                                'end_time'=> $obj->end_time,
                            ]);
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
                're
                peats'=>$req->repeats,
                'user_id'=>$req->user_id,
            ]);

            $id=$queue->id;
            ServiceQueueController::updateService($id,$req->services);

            return  response()->json([
                "result"=>"Queue updated successfully",
                "queue"=>$queue,
                "services"=>ServiceQueueController::getService($queue->id),]);
    }






    public function delete($id)
    {
            $r1=Queue::where('id', $id)->delete();
            if ($r1) {
                return ["result"=>"Queue deleted"];
            } else {
                return ["result"=>"Operation faild"];
            }
    }



























}
