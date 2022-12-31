<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceQueue;
use App\Models\Appointment;
use App\Models\Time;
use App\Http\Controllers\AppointmentController;

use Illuminate\Support\Facades\Validator;

        class ServiceController extends Controller
        {

                function getDetails($id)
                {
                $service=Service::find($id);
                return $service;
                }


                public function addService(request $req)
                {
                        $validator=Validator::make($req->all(),[
                        'name' =>'required|string|max:200',
                        'duration_time' =>'required',
                        'logo'=>'required',
                                ]);



                        if($validator->fails()){

                                return response()->json([
                                'validation_error'=>$validator->messages(),
                                ]);


                        }else{

                                $service=Service::create([
                                'name'=>$req->name,
                                'duration_time'=>$req->duration_time,
                                'logo'=>$req->logo
                                ]);
                                return response()->json([$service ]);
                        }
                }

                // {
                //         "name":"makeup",
                //         "duration_time":"1",
                //         "logo":"image"
                //  }







                function updateDetails(Request $req, $id)
                {
                    $service= Service::find($id);
                    $old_duration_time=$service->duration_time;
                        $service->update([
                        'name' =>$req->name,
                        'logo'=>$req->logo,
                        'duration_time'=>$req->duration_time,
                        ]);

                    if($old_duration_time != $service->duration_time){
                        $queues=ServiceQueue::selectRaw('queue_id')->where('service_id',$service->id)->get();

                        if($queues){
                            for($i=0;$i<sizeof($queues);$i++){
                                $times=Time::where('source_id',$queues[$i]->queue_id)->where('type',"2")->where('status',1)->get();
                                if($times){
                                    for($j=0;$j<sizeof($times);$j++){
                                        $appointments=Appointment::where('time_id',$times[$j]->id)->where('status',1)->orwhere('status',0)->get();
                                        foreach($appointments as $a){
                                            $a->update(['status'=>-1]);
                                            }

                                    AppointmentController::createAppointment(new Request([
                                        'time_id'=>$times[$j]->id,//return the company_id  for this user
                                        'start_time'=>$times[$j]->start_time,
                                        'end_time'=>$times[$j]->end_time,
                                        'source_id'=>$times[$j]->source_id,

                                        ]));

                                    }
                                }
                            }
                        }

                }
                return response()->json([$service]);
            }





                public function delete($id)
                {
                $result= Service::where('id', $id)->delete();
                if ($result) {
                return ["result"=>"service has been delete"];
                } else {
                return ["result"=>"Operation faild"];
                }
                }



                static function getServiceDurationTime($id)
                { $duration_time= Service::selectRaw('duration_time')->where('id',$id);

                return $duration_time;

                }




}
