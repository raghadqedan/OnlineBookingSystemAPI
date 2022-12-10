<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
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
    //need testing
    public function addQueue(request $req)
    {$validator=Validator::make($req->all(),[
        'id'=>'required',
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
         $company_type=CompanyController::getCompanyType($req->input('id'));//من وين بجيب id_country??
       
         $queue= new Queue();
        // //timeQueue

        if($company_type=="0"&count($req->services)>1)
           return response()->json(['message'=>'select only one service Becaus your company system type is time']);
        else{
        $queue->name=$req->input('name');
        $queue->start_regesteration=$req->input('start_regesteration');
        $queue->repeats=$req->input('repeats');
        //$queue->type=$req->input('type');
        //$queue->service_id =$req->input('service_id');
        $queue->user_id=$req->input('user_id');
        
        $result=$queue->save();
        $id=Queue::select('id')->orderBy('created_at', 'desc')->first();
        // //to do search how return the id 
        
          for( $i=0 ;$i<count($req->services);$i++){
            return $result=ServiceQueueController::createServiceQueue($id,$req->services[$i]);
        //     if($result==0){
        //      return ["message"=>"Operation faild"];
        //      }};
        //      return ["message"=>"Queue created Successfully"]; }
        // else{ $result=ServiceQueueController::createServiceQueue($id,$req->input(services[0]));
        //     if($result==0){
        //         return ["message"=>"Operation faild"];
        //         }} 
               
         }
    }
       }}
    // {"id":"22",
    //     "services":[1,2],
    //     "name":"Q1",
    //     "start_regesteration":"2022-12-06 16:00:39",
    //     "repeats":"1",
    //     "user_id":"21"
    //     }
        
  
  //need testing
  public function delete($id)
  {
      $r1=Queue::where('id', $id)->delete();
      $r1=ServiceQueue::delete($id);
      if ($r1&r2) {
          return ["result"=>"Queue has been delete"];
      } else {
          return ["result"=>"Operation faild"];
      }
  }

    
    function updateDetails(Request $req, $id)
        { $queue= Queue::find($id);
        $queue->name=$req->input('name');
        $queue->start_regesteration=$req->input('start_regesteration');
        $queue->repeats=$req->input('repeats');
       // $queue->type=$req->input('type');
        $queue->user_id=$req->input('user_id');
    
        $queue->update();
        return $queue;}


    


}
