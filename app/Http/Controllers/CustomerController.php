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
        }
        else{
        $customer =new Customer;
        $customer->first_name =$req->input('first_name');
        $customer->last_name =$req->input('last_name');
        $customer->email =$req->input('email');
        $customer->password =Hash::make($req->input('password'));
        $customer->save();

        return $customer;}
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
        return $customer;
        
        // return response()->json([
        //     'status'=>200,
        //     'message'=>'Successfully',
        // ]);
        }
        //show customer info
function getCustomer($id)
    {
        $customer= Customer::find($id);
        return $customer;
    }
    //edit,update customer info 
    function editProfile(Request $req,$id)
    {
    $customer= Customer::find($id);
    $customer->first_name =$req->input('first_name');
    $customer->last_name =$req->input('last_name');
    $customer->email =$req->input('email');
    $customer->phone =$req->input('phone');
    $customer->phone =$request->photo->store('image');
    $customer->update();
    return $customer;

  }
  //$req will contain old_password, new_password, confirm_password, customer_id
  function editPassword(Request $req,$id)
  {
  $customer= customer::find($id);
  if(!$customer|| !Hash::check($req->old_password,$customer->password))
        {  
            return response()->json([
                'status'=>401,
                'message'=>'Invalid Credentials'

            ]);
        }
    if($req->new_password&$req->confirm_password){
        $customer->password =$req->input('password');
        $customer->update();
        return response()->json([
            'status'=>200,
            'message'=>' password changed Successfully'

        ]);;
    }
 

}


}
