<?php

namespace App\Http\Controllers;
use App\Models\ServiceQueue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ServiceQueueController extends Controller
{
    static function createServiceQueue($id,$service_id){
        $obj= new ServiceQueue;
        
            $queue_id=$id;
            $obj->queue_id =$queue_id;
            $obj->service_id=$service_id;
           $result= $obj->save();
           return $result;
        

    }
    static function getService($queue_id){
$services=ServicesQueues::selectRaw('service_id')->where('queue_id',$queue_id)->get();
return $services;


    }
    static function updateService($queue_id,$service){
        $services=ServiceQueue::selectRaw('service_id')->where('queue_id',$queue_id)->get();
        return $services;
        
        
            }

    static  public function delete($id)
    {
        $result=ServiceQueue::where('queue_id', $id)->delete();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
    



}
