<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use \Date;
class CustomerController extends Controller
{
    function getAllClient()
        {
            $services=Service::where('company_id',auth()->user()->company_id)->get();//return all services in this company
            if(count($services)>0){
                foreach($services as $s){
                    $books=Booking::where('service_id',$s->id)->get();
                    if(count($books)>0){
                    foreach($books as $b ){
                            $customers[]=Customer::where('id',$b->customer_id)->first();
                    }
                    return   response()->json(["customer"=>$customers]);
                    }else{
                    return   response()->json(["message"=>'opration failed ']);}
                    }
                }
            return   response()->json(["message"=>'opration failed ']);



    }

    function signUp(Request $req)
    {
            $validator= Validator::make($req->all(),[
                'name' =>'required|max:191',
                'phone_number'=>'required',
                'email' =>'required|email|max:191|unique:users,email',
                'password' =>'required',

            ]);

                if($validator->fails()){
                    return response()->json([
                    'validation_error'=>$validator->messages(),
            ]);

            }else{
                    $customer =Customer::create([
                    'name'=>$req->name,
                    'phone_number'=>$req->phone_number,
                    'email'=>$req->email,
                    'password'=>Hash::make($req->password),

            ]);
                    return response()->json([$customer]);}
    }




    function login(Request $req){
        $customer=Customer::where('email',$req->email)->first();
        if(!$customer|| !Hash::check($req->password,$customer->password))
        {
            return response()->json([
                'status'=>401,
                'message'=>'Invalid Credentials'

            ]);
        }
            return response()->json([
            'status'=>200,
            'message'=>'valid Credentials'
        ]);
    }


        //show customer info(profile)
    function getCustomer($id)
    {
        $customer= Customer::find($id);
        return $customer;
    }

    //edit,update customer info
    function updateProfile(Request $req,$id)
    {
    $customer= Customer::find($id);
    $customer->update([
        'first_name' =>$req->first_name,
        'last_name' =>$req->last_name,
        'email' =>$req->email,
        'phone_number' =>$req->image
    ]);

    return response()->json(["customer"=>$customer]);

}


  //not valid
  //  change password $req will contain old_password, new_password, confirm_password, customer_id
// function editPassword(Request $req,$id)
// {
//         $customer= Customer::find($id);
//         if($customer|| !Hash::check($req->old_password,$customer->password))
//                 {
//                     return response()->json([
//                         'status'=>401,
//                         'message'=>'Invalid Credentials'

//                     ]);
//         }
//         if($req->new_password==$req->confirm_password){
//             $customer->update(['password'=>$req->password]);
//             return response()->json([
//                 'status'=>200,
//                 'message'=>' password changed Successfully'

//             ]);
//         }
// }




    // function getAllCompany($category_id){


    // }




    // function getServices($company_id){




    // }


    // function getcompanyDetails($company_id){

    //     //description ,address



    // }


    // function getOnTimes($company_id){

    //         //get all times from time table  for this source_id where status==1

    // }



    function getTotalCustomer($queue_id){
        //get all times from time table  for this source_id where status==1

    }


    function getExpectedWaitingTime($queue_id){
    //get all times from time table  for this source_id where status==1

    }


    function takeNumber($queue_id){
    //get all times from time table  for this source_id where status==1

    }


    function getOnDays($company_id){
        //get company on daynames


 }


 function getAllAvailableAppointment(){




 }


    /// monitor screen , notification api

}
