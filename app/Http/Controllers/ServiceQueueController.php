<?php

namespace App\Http\Controllers;
use App\Models\ServiceQueue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ServiceQueueController extends Controller
{
    static function createServiceQueue($id,$services){

        for( $i=0;$i<count($services);$i++){
            $obj=ServiceQueue::create([
                    'queue_id' =>$id,
                    'service_id'=>$services[$i],
        ]);
        }

    }


    static function getService($queue_id){
            $services=ServiceQueue::selectRaw('service_id')->where('queue_id',$queue_id)->get();
            return $services;


    }
    static function updateService($queue_id,$services){
            $service=ServiceQueue::selectRaw('service_id')->where('queue_id',$queue_id)->delete();

                for( $i=0;$i<count($services);$i++){
                    $obj=ServiceQueue::create([
                    'queue_id' =>$queue_id,
                    'service_id'=>$services[$i],

                ]);}
    }


    // static  public function delete($id)
    // {
    //     $result=ServiceQueue::selectRaw('id')->where('queue_id', $id)->delete();
    //     if ($result) {
    //         return 1;
    //     } else {
    //         return 0;
    //     }
    // }




}
