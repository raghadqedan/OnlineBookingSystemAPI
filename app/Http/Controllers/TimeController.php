<?php

namespace App\Http\Controllers;
use App\Models\Time;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\TimeController;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Booking;
use App\Models\ServiceQueue;
use App\Models\Service;
use Log;


class TimeController extends Controller
{

        static function createTime(Request $req)
            {
                $obj= Time::create([
                'source_id'=>$req->source_id,
                'day'=>$req->day,
                'type'=>$req->type,
                'start_time'=>$req->start_time,
                'end_time'=>$req->end_time,
                'status'=>$req->status
                ]);

                if($obj){
                    if($obj->type=="2"){
                        if($obj->status=="1"){
                    $result=AppointmentController::createAppointment(new Request([
                        'time_id'=>$obj->id,//return the company_id  for this user
                        'start_time'=>$obj->start_time,
                        'end_time'=>$obj->end_time,
                        'source_id'=>$obj->source_id,
                        'status'=>"0"
                    ]   ));
                    }else{
                        $result=AppointmentController::createAppointment(new Request([
                            'time_id'=>$obj->id,//return the company_id  for this user
                            'start_time'=>$obj->start_time,
                            'end_time'=>$obj->end_time,
                            'source_id'=>$obj->source_id,
                            'status'=>"10"
                        ]   ));



                }




                    if($result=="1") {
                        return response()->json(['b'=>'1',
                                            'time'=>$obj]);
                    }
                return response()->json(['b'=>'0']);

            }



    }
            }
        //get schedule times for the source_id (return array )
            function getscheduleTime($source_id,$type)
            {
                $obj=Time::where('source_id',$source_id)
                ->where('type',$type)
                ->where('status',1)
                ->get();

                return response()->json(['time'=>$obj]);
            }


        //return the start and end time for one obj
        static function getTimes($source_id,$type,$day)
        {
                $obj=Time::where('source_id',$source_id)
                ->where('type',$type)
                ->where('day',$day)
                ->first();

                return $obj;
            }


            //return only start time for only one obj
        static function getStartTime($source_id ,$type,$day )
            {
                $obj= Time::selectRaw('start_time')
                ->where('source_id',$source_id)
                ->where('day',$day)
                ->where('type',$type)
                ->first();
            return $obj;
            }


    //return only endtime for only one obj
        static function getEndTime($source_id,$type,$day)
        {   $obj= Time::selectRaw('end_time')
                    ->where('source_id',$source_id)
                    ->where('day',$day)
                    ->where('type',$type)
                    ->first();
                return $obj;
        }



            // function setEndTime($source_id ,$type,$day ,$end_time)
            // {
            //     $obj=Time::where('source_id',$source_id)->where('type',$type)->where('day',$day)->get();

            //     $obj->toQuery()->update(['end_time'=>$end_time,]);

            // }

//valid
        static  public function updateQueueTime(Request $req){

                    $parent_type="1";
                    $parent_source_id=Queue::selectRaw('user_id')->where('id',$req->source_id)->first();//return the user_id who control this queue
                    // 'source_id'=>$req->source_id,
                    // 'type'=>$req->type,//put by default
                    // 'start_time'=>$req->start_time,
                    // 'end_time'=>$req->end_time,
                    // 'day'=>$req->day


                $times=Time::where('source_id',$parent_source_id->user_id)
                ->where('type',$parent_type)
                ->where('day',$req->day)
                ->first();


                if(($req->start_time >= $times->start_time)&&($req->end_time <= $times->end_time)&&($req->start_time <= $req->end_time))
                    {
                        $obj= Time::where('source_id',$req->source_id)->where('type',2)->where('day',$req->day)->where('status',1)->first();

                        if($obj){
                        $service_id=ServiceQueue::selectRaw('service_id')->where('queue_id',$obj->source_id)->first();
                        $duration_time= Service::selectRaw('duration_time')->where('id',$service_id->service_id)->first();//error
                              ////

                        if(($req->start_time!=$obj->start_time)||($req->end_time!=$obj->end_time)){

                                if($req->start_time<=(date("H:i:s",strtotime($obj->start_time)-strtotime($duration_time->duration_time)))){

                                        AppointmentController::createAppointment(new Request([
                                        'time_id'=>$obj->id,//return the company_id  for this user
                                        'start_time'=>$req->start_time,
                                        'end_time'=>$obj->start_time,
                                        'source_id'=>$obj->source_id,

                                        ]));

                                    }
                                if($req->end_time>=date("H:i:s",strtotime($obj->end_time)+strtotime($duration_time))){

                                    AppointmentController::createAppointment(new Request([
                                    'time_id'=>$obj->id,//return the company_id  for this user
                                    'start_time'=>$obj->end_time,
                                    'end_time'=>$req->end_time,
                                    'source_id'=>$obj->source_id,
                                    ]));
                                }

                                elseif($req->start_time>=$obj->start_time&&$req->end_time<=$obj->end_time){

                                            $appointments=Appointment::where('time_id',$obj->id)->where('status',1)->orwhere('status',0)->get();

                                            if($appointments){

                                                for($i=0;$i<sizeOf($appointments);$i++){
                                                                //any appoitment is out of  new time bounds of the queue will satisfy this if condition
                                                            if(($appointments[$i]->start_time < $req->start_time &&$appointments[$i]->end_time>$req->start_time)||($appointments[$i]->start_time < $req->start_time &&$appointments[$i]->end_time<$req->start_time)||($appointments[$i]->start_time<$req->end_time&&$appointments[$i]->end_time>$req->end_time)||$appointments[$i]->start_time>$req->end_time)
                                                            {
                                                                $booking=Booking::where('appointment_id',$appointments[$i]->id)->where('status',0)->orwhere('status',1)->first();
                                                                if($booking){
                                                                    $booking->update(['status'=>3]);// set booking status=3 mean this booking is canceled
                                                                    //TODO:send email "your booking cancelled please book anew book in another time"
                                                                    }
                                                                $appointments[$i]->update(['status'=>-1]);
                                                            }

                                                }

                                        }}


                                    $r =$obj->update([
                                        'start_time'=>$req->start_time,
                                        'end_time'=>$req->end_time
                                    ]);

                                    return response()->json([
                                        'message'=>'Queue updated successfully',
                                        'b'=>'1']);



                        }
                        return response()->json([
                            'message'=>'These times ​​already exist',
                            'b'=>'0']);
                    }


                    }

                    return response()->json([
                        'message'=>'Operation faild',
                        'b'=>'0']);
        }




            static  public function updateUserTime(Request $req){

                $parent_type="0";
                $parent_source_id=auth()->user()->company_id;//return the company_id  for this user
                $times=Time::where('source_id',$parent_source_id)
                ->where('type',$parent_type)
                ->where('day',$req->day)
                ->first();

                if(($req->start_time >= $times->start_time)&&($req->end_time <= $times->end_time)&&($req->start_time <= $req->end_time))
                {
                    $obj= Time::where('source_id',$req->source_id)->where('type',"1")->where('day',$req->day)->first();

                    $obj->update([
                        'start_time'=>$req->start_time,
                        'end_time'=>$req->end_time
                    ]);

                    $queues=Queue::where('user_id',$req->source_id)->get();
                    for($i=0;$i<count($queues);$i++)
                    {   $request = new Request([
                            'source_id'=>$queues[$i]->id,//return the company_id  for this user
                            'start_time'=>$req->start_time,
                            'end_time'=>$req->end_time,
                            'day'=>$req->day ]);
                            $result=TimeController::updateQueueTime($request);
                    }

                    return response()->json([
                        'message'=>'user updated successfully',
                        'b'=>'1']);
                }
                else{
                    return response()->json([
                        'message'=>'Operation faild ',
                        'b'=>'1']);
                    }}



            static public function updateCompanyTime(Request $req){

                $companyTime=Time::where('source_id',auth()->user()->company_id)->where('type',"0")->where('day',$req->day)->first();

                $companyTime->update([
                    'start_time'=>$req->start_time,
                    'end_time'=>$req->end_time
                ]);

                $users=User::where('company_id',auth()->user()->company_id)->get();


                for($i=0;$i<sizeof($users);$i++){
                    $request=new Request([
                        'source_id'=>$users[$i]->id,//return the company_id  for this user
                        'start_time'=>$req->start_time,
                        'end_time'=>$req->end_time,
                        'day'=>$req->day
                    ]);

                    $result = json_decode(TimeController::updateUserTime($request)->getContent(), true);

                    if($result['b']=="0"){
                        return response()->json([
                            'message'=>'Operation faild '
                        ]);
                        }
                    }
                return response()->json([
                    'message'=>' company updated successfully' ]);

            }





            function setQueueOffDay(Request $req){

                $obj=Time::where('source_id',$req->source_id)->where('type',"2")->where('day',$req->day)->where('status',1)->first();

                if($obj){
                        $appointments=Appointment::where('time_id',$obj->id)->whereIn('status',[1,0])->get();

                    if($appointments){

                    for($i=0;$i<sizeOf($appointments);$i++){
                        $booking=Booking::where('appointment_id',$appointments[$i]->id)->where('status',0)->orwhere('status',1)->first();

                                        //status=0 mean the confirmed booking status =1 mean the turned booking status=2 canceled booking status=3 mean checkedout boooking
                                if($booking){
                                        $booking->update(['status'=>3]);
                                    //TODO:send email "your booking cancelled please book anew book in another time"//because company put this day as off day
                                    //email   "your booking cancelled please book anew book in another time";
                                }
                            $appointments[$i]->update(['status'=>10]); //make the appointment is available
                        }
                    }

                $obj->update(['status'=>0]);//status=0 mean this time is off day ,status=1 mean this time is on day
                return  response()->json([ 'message'=>'Set as off day successfully',
                                            'b'=>'1' ]);

                }else{
                return  response()->json([ 'message'=>'opration failed  beacause this day is already off',
                                        'b'=>'1' ]);
                }

        }


                                //   setOnDay or setOffday need
                                //         "source_id":"1",
                                //           "day":"1"
                                //         }


        //need testing
            function setUserOffDay(Request $req){

                $obj=Time::where('source_id',$req->source_id)->where('type',"1")->where('day',$req->day)->where('status',1)->first();

                if($obj){
                $queues=Queue::where('user_id',$req->source_id)->get();

                    if($queues){
                    for($i=0;$i<sizeOf($queues);$i++){
                        $request=new Request([
                            'source_id'=>$queues[$i]->id,
                            'day'=>$req->day
                        ]);

                        $result = json_decode($this->setQueueOffDay( $request)->getContent(), true);

                        if($result['b']=="0"){
                            return response()->json([
                                'message'=>'Operation faild '
                            ]);
                            }
                        }
                        $r= $obj->update(['status'=>0]);
                        if($r){
                            return response()->json([
                                'message'=>'set as user offday successfully',
                                'b'=>'1' ]);

                        }
                        else {
                            return response()->json([
                                'message'=>'operation faild',
                                'b'=>'0' ]);
                        }
                    }
                }else {
                    return response()->json([
                        'message'=>'operation faild beacause this day is already off',
                        'b'=>'1' ]);
                }

            }



            function setCompanyOffDay(Request $req){

                $obj=Time::where('source_id',$req->source_id)->where('type',"0")->where('status',1)->where('day',$req->day)->first();

                if($obj){
                    $users=User::where('company_id',$req->source_id)->get();
                    if($users){
                    for($i=0;$i<sizeOf($users);$i++){
                        $request=new Request([
                            'source_id'=>$users[$i]->id,
                            'day'=>$req->day
                        ]);

                        $result = json_decode($this->setUserOffDay($request)->getContent(), true);
                        if($result['b']=="0"){
                            return response()->json([
                                'message'=>'Operation faild '
                            ]);
                            }
                        }

                        $r= $obj->update(['status'=>0]);
                        if($r){
                            return response()->json([
                                'message'=>'set as company offday successfully',
                                'b'=>'1' ]);

                        }
                        else {
                            return response()->json([
                                'message'=>'operation faild  beacause this day is already off',
                                'b'=>'0' ]);
                        }
                    }
                }else {
                    return response()->json([
                        'message'=>'operation faild beacause this day is already off',
                        'b'=>'0' ]);
                }
            }



            function setQueueOnDay(Request $req){

                    $obj=Time::where('source_id',$req->source_id)->where('type',"2")->where('day',$req->day)->where('status',0)->first();

                if($obj){
                    $appointments=Appointment::where('time_id',$obj->id)->where('status',10)->get();

                    if($appointments){
                            for($i=0;$i<sizeOf($appointments);$i++){
                                $appointments[$i]->update(['status'=>0]); //make the appoiintment is available
                            }
                    }

                    $obj->update(['status'=>1]);//status=0 mean this time is off day ,status=1 mean this time is on day
                    return  response()->json([ 'message'=>'Set as on day successfully',
                                                'b'=>'1' ]);

                }else{
                return  response()->json([ 'message'=>'opration failed  beacause this day is already on',
                                        'b'=>'1' ]);
                }
    }





                function setUserOnDay(Request $req)
                {
                    $obj=Time::where('source_id',$req->source_id)->where('type',"1")->where('day',$req->day)->where('status',0)->first();

                    if($obj){
                    $queues=Queue::where('user_id',$req->source_id)->get();

                        if($queues){
                            for($i=0;$i<sizeOf($queues);$i++){
                                $request=new Request([
                                    'source_id'=>$queues[$i]->id,
                                    'day'=>$req->day
                                ]);

                                $result = json_decode($this->setQueueOnDay($request)->getContent(), true);

                                if($result['b']=="0"){
                                    return response()->json([
                                        'message'=>'Operation faild '
                                    ]);
                                    }
                            }
                            $r= $obj->update(['status'=>1]);
                            if($r){
                                return response()->json([
                                    'message'=>'set as user on day successfully',
                                    'b'=>'1' ]);

                            }
                            else{
                                return response()->json([
                                    'message'=>'operation faild',
                                    'b'=>'0' ]);
                            }
                        }

                    }else {
                            return response()->json([
                                'message'=>'operation faild beacause this day is already on',
                                'b'=>'1' ]);
                    }

        }



                function setCompanyOnDay(Request $req){

                    $obj=Time::where('source_id',$req->source_id)->where('type',0)->where('status',0)->where('day',$req->day)->first();

                    if($obj){
                    $users=User::where('company_id',$req->source_id)->get();

                        if($users){
                        for($i=0;$i<sizeOf($users);$i++){
                            $request=new Request([
                                'source_id'=>$users[$i]->id,
                                'day'=>$req->day
                            ]);

                            $result = json_decode($this->setUserOnDay($request)->getContent(), true);

                            if($result['b']=="0"){
                                return response()->json([
                                    'message'=>'Operation faild '
                                ]);
                                }
                            }
                            $r= $obj->update(['status'=>1]);
                            if($r){
                                return response()->json([
                                    'message'=>'set as company on day successfully',
                                    'b'=>'1' ]);

                            }
                            else {
                                return response()->json([
                                    'message'=>'operation faild  beacause this day is already on',
                                    'b'=>'0' ]);
                            }
                        }
                    }else {
                        return response()->json([
                            'message'=>'operation faild beacause this day is already on',
                            'b'=>'0' ]);
                    }
                }


            }





























//request will have $source_id,$type,$day
//     function setOnDay(Request $req)
//         {
//             $request=new Request([
//                 'source_id'=>$req->source_id,//return the company_id  for this user
//                 'start_time'=>"08:00:00",
//                 'end_time'=>"14:00:00",
//                 'day'=>$req->day,
//                 'type'=>$req->type
//             ]);

//             $result =$this->createTime($request);

//             if($result)
//             {
//                 if($req->type=="0"){

//                     $r=new Request([
//                         'source_id'=>$req->source_id,//return the company_id  for this user
//                         'start_time'=>$req->start_time,
//                         'end_time'=>$req->end_time,
//                         'day'=>$req->day,
//                     ]);

//                     $this->updateCompanyTime($r);

//                 }elseif($req->type=="1"){

//                     $this->updateUserTime($request);

//                 }else{$this->updateQueueTime($request);

//                 }

//                 return response()->json([ 'message'=>'set as off day successfully' ]);

//             }else{return response()->json(['message'=>' opration faild' ]);}







//         }











 //createTime or update Queue,user,company times send this  jsonfile $req
            //    {"type":"0",//tis need only in setOnDay,SetOffDay
            //     "source_id":"1",
            //     "start_time":"07:55:34",
            //     "end_time":"07:55:34",
            //       "day":"1"
            //     }


















//     function setStartTime($source_id, $type ,$day , $start_time)
    // {
    //     $obj= Time::where('source_id',$source_id)
    //     ->where('type',$type)
    //     ->where('day',$day)
    //     ->get();
    //     $obj->toQuery()->update(['start_time'=>$start_time,]);
    // }



//method to update start time and end time .
    // public function updateTime($source_id,$type,$day,Request $req)
    // {
    //     $time= Time::where('source_id',$source_id)
    //     ->where('type',$type)
    //     ->where('day',$day)
    //     ->get();
    //     $time->toQuery()->update(['start_time'=>$req->input('start_time'),'end_time'=>$req->input('end_time')]);











    // $obj=Time::where('source_id',$req->source_id)->where('type',"2")->where('day',$req->day)->where('status',1)->first();

    // if($obj){
    // $appointments=Appointment::where('time_id',$obj->id)->where('status',0)->orwhere('status',1)->get();

    //     if($appointments){
    //         for($i=0;$i<sizeOf($appointments);$i++){
    //                     $booking=Booking::where('appointment_id',$appointments[$i]->id)->where('status',0)->orwhere('status',1)->first();

    //                     //status=0 mean the confirmed booking status =1 mean the turned booking status=2 canceled booking status=3 mean checkedout boooking
    //                     if($booking){
    //                         $booking->update(['status'=>3]);
    //                         //email   "your booking cancelled please book anew book in another time";
    //                     }

    //                     $appointments[$i]->update(['status'=>10,]); //status=0 mean the appointment is available status =1 mean the appointment is booked status =10 mean the appointment  offstatus =-1 mean the appointment  deleted


    //                 }
    // }

    //     $obj->update(['status'=>0]);//status=0 mean this time is off day ,status=1 mean this time is on day
    //     return  response()->json([ 'message'=>'set as off day successfully',
    //                                 'b'=>'1' ]);

    // }else{
    // return  response()->json([ 'message'=>'opration failed  beacause this day is already off',
    //                         'b'=>'1' ]);
    // }
