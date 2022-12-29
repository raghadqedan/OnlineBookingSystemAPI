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
                'end_time'=>$req->end_time, ]);

                if($obj){
                    if($obj->type=="2"){

                    $result=AppointmentController::createAppointment($obj);
                    return $result;
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
                $obj=Time::selectRaw('start_time,end_time')
                ->where('source_id',$source_id)
                ->where('type',$type)
                ->get();

                return response()->json([$obj]);
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
    static  public function updateQueueTime(Request $req)
        {

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
                    $obj= Time::where('source_id',$req->source_id)->where('type',"2")->where('day',$req->day)->first();

                    $obj->update([
                        'start_time'=>$req->start_time,
                        'end_time'=>$req->end_time
                    ]);

            // todo update the children  appoitments in this queue
             // todo  may need to delete customersbooking and  send notification to them , message"sorry,your booking canceled" if the updated time effect in them booking


                    return response()->json([
                            'message'=>'Queue updated successfully',
                            'b'=>'1']);
                }
            else{
            return response()->json([
                'message'=>'Operation faild',
                'b'=>'0']);
            }
    }




        static  public function updateUserTime(Request $req)
        {   $parent_type="0";
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



                static public function updateCompanyTime(Request $req)
                {
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




//need testing
    function setQueueOffDay(Request $req)
    {
        $obj=Time::where('source_id',$req->source_id)->where('type',"2")->where('day',$req->day)->first();

        if($obj){
        $appointments=Appointment::where('time_id',$obj->id)->get();
            if($appointments){
            for($i=0;$i<sizeOf($appointments);$i++){

                    $bookings=Booking::where('appointment_id',$appointments[$i]->id)->where('status',0)->orwhere('status',1)->first();
                    //status=0 mean the confirmed booking status =1 mean the turned booking status=2 checkedout booking status=3 mean canceled boooking
                    if($bookings){
                        //TODO:send email "your booking cancelled please book anew book in another time"//because company put this day as off day
                        //TODO:set status in the booking==3 canceled
                        //email   "your booking cancelled please book anew book in another time";

                    }

                    $appointments[$i]->delete();
            }
        }
        $obj->delete();
        return  response()->json([ 'message'=>'set as off day successfully',
                                    'b'=>'1' ]);

        }else{
        return  response()->json([ 'message'=>'opration failed',
                                'b'=>'0' ]);
        }
}


                        //   setOnDay or setOffday need
                        //         "source_id":"1",
                        //           "day":"1"
                        //         }


//need testing
        function setUserOffDay(Request $req){
            $obj=Time::where('source_id',$req->source_id)->where('type',"1")->where('day',$req->day)->first();

            if($obj){
            $queues=Queue::where('user_id',$obj->id)->get();
                if($queues){
                for($i=0;$i<sizeOf($queues);$i++){
                    $request=new Request([
                        'source_id'=>$queues[$i]->id,
                        'day'=>$req->day
                    ]);

                    $result = json_decode($this->setQueueOffDay()->getContent(), true);

                    if($result['b']=="0"){
                        return response()->json([
                            'message'=>'Operation faild '
                        ]);
                        }
                    }
                    $r=$obj->delete();
                    if($r){
                        return response()->json([
                            'message'=>'set as useroff day successfully',
                            'b'=>'1' ]);

                    }
                    else {
                        return response()->json([
                            'message'=>'operation faild',
                            'b'=>'0' ]);
                    }
                }
            }

    }

//need testing
    function setCompanyOffDay(Request $req){
        $obj=Time::where('source_id',$req->source_id)->where('type',"0")->where('day',$req->day)->first();

        if($obj){
        $users=User::where('company_id',$obj->id)->get();
            if($users){
            for($i=0;$i<sizeOf($users);$i++){
                $request=new Request([
                    'source_id'=>$users[$i]->id,
                    'day'=>$req->day
                ]);

                $result = json_decode($this->setUserOffDay->getContent(), true);

                if($result['b']=="0"){
                    return response()->json([
                        'message'=>'Operation faild '
                    ]);
                    }
                }
                $r=$obj->delete();
                if($r){
                    return response()->json([
                        'message'=>'set as companyoff day successfully',
                        'b'=>'1' ]);

                }
                else {
                    return response()->json([
                        'message'=>'operation faild',
                        'b'=>'0' ]);
                }
            }
        }

}


//TODO
        function setQueueOnDay(Request $req)
        {
            $request=new Request([
                'source_id'=>$req->source_id,//return the company_id  for this user
                'start_time'=>"08:00:00",//todo :must put as theuser  parent times
                'end_time'=>"14:00:00",
                'day'=>$req->day,
                'type'=>"2"

            ]);


            $result = json_decode(TimeController::createTime($request)->getContent(), true);

            if($result['b']=="1")
            {return response()->json([ 'message'=>'set as off day successfully',
                    'queue'=>$result['time'] ]);

            }else{
                return response()->json(['message'=>' opration faild' ]);
            }
}

function setUserOnDay(Request $req)
        {
            $request=new Request([
                'source_id'=>$req->source_id,//return the company_id  for this user
                'start_time'=>"08:00:00",
                'end_time'=>"14:00:00",
                'day'=>$req->day,
                'type'=>"1"

            ]);


            $result = json_decode(TimeController::createTime($request)->getContent(), true);

            if($result['b']=="1")
            { $queues=Queue::where('user_id',$request->id)->get();
                if($queues){
                for($i=0;$i<sizeOf($queues);$i++){
                    $r=new Request([
                        'source_id'=>$req->source_id,//return the company_id  for this user
                        'start_time'=>"08:00:00",//todo :must put as the  company parent times
                        'end_time'=>"14:00:00",
                        'day'=>$req->day,
                        'type'=>"2"

                    ]);
                    $result = json_decode($this->setQueueODay()->getContent(), true);
                    if($ressult){
                            return response()->json([ 'message'=>'set as on userday successfully',
                                                    'user'=>$result['time'] ]);}
                        else{
                            return response()->json(['message'=>' opration faild' ]);
                            }
                }
                }
            }
}


//need testing
function setCompanyOnDay(Request $req)
        {
            $request=new Request([
                'source_id'=>$req->source_id,//return the company_id  for this user
                'start_time'=>"08:00:00",
                'end_time'=>"14:00:00",
                'day'=>$req->day,
                'type'=>"0"

            ]);


            $result = json_decode(TimeController::createTime($request)->getContent(), true);

            if($result['b']=="1")
            { $users=User::where('company_id',$request->id)->get();
                if($users){
                for($i=0;$i<sizeOf($users);$i++){
                    $request=new Request([
                        'source_id'=>$users[$i]->id,
                        'start_time'=>"08:00:00",
                        'end_time'=>"14:00:00",
                        'day'=>$req->day,
                        'type'=>"1"
                    ]);

                    $result = json_decode($this->setUserOnDay->getContent(), true);

                    if($result['b']=="0"){
                        return response()->json([
                            'message'=>'Operation faild '
                        ]);
                    }

                }

                    return response()->json([ 'message'=>'set as on companyday successfully',
                                                'company'=>$result['time'] ]);}



                }
                else{
                    return response()->json(['message'=>' opration faild' ]);
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
