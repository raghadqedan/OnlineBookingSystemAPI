<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Booking;
use App\Models\Customer;
class ControlQueues extends Controller
{
    public function getCurrentCustomer($queue_id){
            $company_type=Company::selectRaw('type')->where('id',auth()->user()->company_id)->where('status',1)->first();
                if( $company_type->type==0){//if companytype is number
                //return the turned customer id to this queue if exist
                        $currentCustomerId=Booking::selectRaw('customer_id')->where('status',1)->where('turned_queue',$queue_id)->first();
                        return $currentCustomerId;
                        if($currentCustomerId){
                                $customer= Customer::where('id',$currentCustomerId)->first();
                                return response()->json(['customer'=>$customer ]);
                        }else{
                            $currentCustomerId=Booking::selectRaw('customer_id')->where('queue_id',$queue_id)->where('status',0)->first();
                            if($currentCustomerId){
                                    $customer= Customer::where('id',$currentCustomerId)->first();
                                    return response()->json(['customer'=>$customer ]);}
                                }}
                                return  response()->json(['message'=>"operation failed" ]);



    }
}
