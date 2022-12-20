<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class CustomerController extends Controller
{
    
    function signUp(Request $req)
    {
            $validator= Validator::make($req->all(),[
                'first_name' =>'required|max:191',
                'last_name' =>'required|max:191',
                'email' =>'required|email|max:191|unique:users,email',
                'password' =>'required',

            ]);
                
            if($validator->fails()){
                    return response()->json([
                    'validation_error'=>$validator->messages(),
            ]);
            
             }else{
                    $customer =Customer::create([
                    'first_name'=>$req->first_name,
                    'last_name'=>$req->last_name,
                    'email'=>$req->email,
                    'password'=>Hash::make($req->password),
                    'phone_number'=>Hash::make($req->password),
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
  function editPassword(Request $req,$id)
  {
        $customer= Customer::find($id);
        if($customer|| !Hash::check($req->old_password,$customer->password))
                {  
                    return response()->json([
                        'status'=>401,
                        'message'=>'Invalid Credentials'

                    ]);
        }
        if($req->new_password==$req->confirm_password){
           $customer->update(['password'=>$req->password]);
            return response()->json([
                'status'=>200,
                'message'=>' password changed Successfully'

            ]);;
        }
 }
}
