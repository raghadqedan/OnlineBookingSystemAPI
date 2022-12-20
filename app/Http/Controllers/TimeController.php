<?php

namespace App\Http\Controllers;
use App\Models\Time;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\TimeController;
use App\Models\User;


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
        {  
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
                    return $obj;
                $queues=Queue::where('user_id',$req->source_id)->get();
                for($i=0;$i<count($queues);$i++)
                {   $request = new Request([
                        'source_id'=>$queues[$i]->id,//return the company_id  for this user 
                        'start_time'=>$req->start_time,
                        'end_time'=>$req->end_time,
                        'day'=>$req->day

                    ]); 
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
                                'end_time'=>$req->end_time]);
                     
                                $users=User::where('company_id',auth()->user()->company_id)->get();
                              
                           
                                for($i=0;$i<sizeof($users);$i++)
                                {   $request=new Request([
                                        'source_id'=>$users[$i]->id,//return the company_id  for this user 
                                        'start_time'=>$req->start_time,
                                        'end_time'=>$req->end_time,
                                        'day'=>$req->day
                
                                    ]); 
                               
                                    $result=TimeController::updateUserTime($request);
                                     return $result;
                                    if($result=="0"){
                                       return response()->json([
                                          'message'=>'Operation faild ']);
                                     }
                                 }
                                return response()->json([
                                    'message'=>' company updated successfully' ]);
                            
    
                                }

                            }
 //createTime or update Queue,user,company times send this  jsonfile $req
            //    {
            //     "source_id":"1",
            //     "start_time":"07:55:34",
            //     "end_time":"07:55:34"
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
