<?php

namespace App\Http\Controllers;
use App\Models\Time;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\TimeController;

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

            // todo createAppoitments if type ==2
            
                return 0;}
        
        //get schedule times for the source_id (return array )
            function getscheduleTime($source_id,$type)
            {
                $obj=Time::selectRaw('start_time,end_time')
                ->where('source_id',$source_id)
                ->where('type',$type)
                ->first();
                
                return response()->json([$obj]);
            }


        //return the start and end time for one obj
        static function getTimes($source_id,$type,$day)
            {
                $obj=Time::selectRaw('start_time,end_time')
                ->where('source_id',$source_id)
                ->where('type',$type)
                ->where('day',$day)
                ->first();
                
                return response()->json([$obj]);
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


        public function updateQueueTime(Request $req)
        {
            $request = new Request([
                'type'=>"2",
                'source_id'=>Queue::selectRaw('user_id')->where('id',$req->source_id)->first(),//return the user_id who control this queue
                'array'=>$req
            ]);
            $result=TimeController::updateTime($request);
            if($result==1)
            {// todo update the children  appoitments in this queue 
             // todo  may need to delete customersbooking and  send notification to them , message"sorry,your booking canceled" if the updated time effect in them booking 
            
                return response()->json([
                "message"=>"Queue updated successfully "]);
            }
            else return response()->json([
                "message"=>"Operation faild"]);

        }



        public function updateUserTime(Request $req)
        {
            $request = new Request([
                'type'=>"0",
                'source_id'=>auth()->user()->company_id,//return the company_id  for this user 
                'array'=>$req
            ]);
            $result=TimeController::updateTime($request);
            if($result==1)
                {// if the usertimes updated successfully parent user is updated so the children queues must update   
                    $queues=Queue::where('user_id',$req->source_id)->get();
                    for($i=0;$i<count($queues);$i++)
                    {   $request = new Request([
                            'type'=>"1",
                            'source_id'=>auth()->user()->company_id,//return the company_id  for this user 
                            'day'=>$req
                        ]);
                            $result=TimeController::updateQueueTime($request);
                            if( $result==0){
                                return response()->json([
                                    "message"=>"Operation faild"]);
                                }
                            
                    }
                    return response()->json([
                        "message"=>"user updated successfully "]);
                    }
                    else{
                        return response()->json([
                            "message"=>"Operation faild beacause company schedule times can not updated" ]);
                        };

        }
                    

                


        public function updateCompanyTime(Request $req)
        {

            $obj->update([
                'source_id'=>$req->source_id,
                'day'=>$req->day,
                'type'=>$req->type,
                'start_time'=>$req->start_time,
                'end_time'=>$req->end_time]);

            $result=TimeController::updateUserTime();
            
            if($result==1)
                {// if the usertimes updated successfully parent user is updated so the children queues must update   
                    $queues=Queue::where('user_id',$req->source_id)->get();
                    for($i=0;$i<count($queues);$i++)
                    {   $request = new Request([
                            'type'=>"1",
                            'source_id'=>auth()->user()->company_id,//return the company_id  for this user 
                            'day'=>$req->day
                        ]);
                            $result=TimeController::updateTime($request);
                            if( $result==0){
                                return response()->json([
                                    "message"=>"Operation faild"]);
                                }
                            
                    }
                }else{
                return response()->json([
                    "message"=>"Operation faild beacause user schedule times can not updated" ]);
                };


        }
                



        public function updateTime(Request $request)
        {  
                $Times=TimeController::getTimes( $request->source_id,$request->type,$request->array['day']);
            if($request->array['start_time']>=$Times->start_time||$request->array['end_time']<=$Times->end_time)
                { $obj= Time::where('source_id',$request->array['source_id'])->where('type',$request->array['type'])->where('day',$request->array['day'],)->first();  

                    $obj->update([
                        'source_id'=>$request->array['source_id'],
                        'day'=>$request->array['day'],
                        'type'=>$request->array['type'],
                        'start_time'=>$request->array['start_time'],
                        'end_time'=>$request->array['end_time']]);
                        
                    return 1;
            }else{
                    return 0;
                    }  

        }


 //createTime or update Queue,user,company times send this  jsonfile $req
            //    {
            //     "source_id":"1",
            //     "type":"0",
            //     "start_time":"07:55:34",
            //     "end_time":"07:55:34"
            //     }














}


 
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
    //     }