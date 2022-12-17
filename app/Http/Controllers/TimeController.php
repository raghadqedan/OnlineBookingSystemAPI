<?php

namespace App\Http\Controllers;
use App\Models\Time;
use Illuminate\Http\Request;


class TimeController extends Controller
{

//to do set function 

     static function createTime(Request $req)
     {  $obj= Time::create([
        'source_id'=>$req->source_id,
        'day'=>$req->day,
        'type'=>$req->type,
        'start_time'=>$req->start_time,
        'end_time'=>$req->end_time,

     ]);
    return 0;}
     //if the source_id for acompany 
    //     if('type'=="0"){
    //         $obj= Time::create([
    //             'source_id'=>$req->source_id,
    //             'day'=>$req->day,
    //             'type'=>$req->type,
    //             'start_time'=>$req->start_time,
    //             'end_time'=>$req->end_time,
        
    //          ]);
    //     elseif($req-->input('type'==1)){
    //     $obj->start_time =$req->input('start_time');
    //     $obj->end_time =$req->input('end_time');  
    //     }
    //     elseif($req-->input('type'==3)){
    //        $s=Queue::find('service_id')
    //        ->where('id',$req->input('source_id'));
    //        $serivce=Service::where('id',$s)->get(); 
    //        $obj->start_time =$service->toQuery()->get('start_time');
    //        $obj->end_time =$service->toQuery()->get('end_time');
    // //         }
    // {$obj= new Time;
        
            
            // $result=$obj->save();
            // if($result=1&$req->input('source_id')==2&(CompanyController::getCompanyType($company_id))==0){
            //  AppoitmentController::createAppoitment($obj->id,$req->input('source_id')==2,$req->input('start_time'),$req->input('end_time'));}
            // else{
            //     return ["message"=>"Operation faild"];
            // }}
    
    //createTime jsonfile
    //    {
    //     "source_id":"1",
    //     "type":"0",
    //     "start_time":"07:55:34",
    //     "end_time":"07:55:34"
    //     }

//method to update start time and end time .
    // public function updateTime($source_id,$type,$day,Request $req)
    // {
    //     $time= Time::where('source_id',$source_id)
    //     ->where('type',$type)
    //     ->where('day',$day)
    //     ->get();
    //     $time->toQuery()->update(['start_time'=>$req->input('start_time'),'end_time'=>$req->input('end_time')]);
    //     }


//get scedule times for the source_id 
    function getscheduleTime($source_id,$type)
    {
        $obj=Time::selectRaw('start_time,end_time')
        ->where('source_id',$source_id)
        ->where('type',$type)
        ->get();
        
        return $obj;
    }

    function getStartTime($source_id ,$type,$day )
    {
        $obj= Time::selectRaw('start_time')
        ->where('source_id',$source_id)
        ->where('day',$day)
        ->where('type',$type)
        ->get();
    return $obj;
    }

    //     function setStartTime($source_id, $type ,$day , $start_time)
    // {
    //     $obj= Time::where('source_id',$source_id)
    //     ->where('type',$type)
    //     ->where('day',$day)
    //     ->get();
    //     $obj->toQuery()->update(['start_time'=>$start_time,]);
    // }

    

        function getEndTime($source_id,$type,$day)

        {
           // $obj= Time::where('source_id',$source_id)->where('$day',$day)->where('type',$type)->toQuery()->get();
            $obj= Time::selectRaw('end_time')
                ->where('source_id',$source_id)
                ->where('day',$day)
                ->where('type',$type)
                ->get();
            return $obj;
        }


        // function setEndTime($source_id ,$type,$day ,$end_time)
        // {
        //     $obj=Time::where('source_id',$source_id)->where('type',$type)->where('day',$day)->get();
            
        //     $obj->toQuery()->update(['end_time'=>$end_time,]);
           
        // }

    
    }
