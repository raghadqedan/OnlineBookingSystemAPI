<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;

class QueueController extends Controller
{
    
    function getDetails($id)
    {
     $queue=Queue::find($id);
   
       return$queue;
    }

    public function addQueue(request $req)
    {
        $queue= new Queue();
        $queue->name=$req->input('name');
        $queue->start_regesteration=$req->input('start_regesteration');
        $queue->repeats=$req->input('repeats');
        $queue->type=$req->input('type');
        $queue->service_id =$req->input('service_id');
        $queue->user_id=$req->input('user_id');
        $queue->save();
        return $queue;
    }
  
  
  
  public function delete($id)
  {
      $result= Service::where('id', $id)->delete();
      if ($result) {
          return ["result"=>"user has been delete"];
      } else {
          return ["result"=>"Operation faild"];
      }
  }


    function updateDetails(Request $req, $id)
        { $queue= Queue::find($id);
        $queue->name=$req->input('name');
        $queue->start_regesteration=$req->input('start_regesteration');
        $queue->repeats=$req->input('repeats');
        $queue->type=$req->input('type');
        $queue->service_id =$req->input('service_id');
        $queue->user_id=$req->input('user_id');
    
        $queue->update();
        return $queue;}





}
