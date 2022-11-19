<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class CompanyController extends Controller
{  function register(Request $req)
    {
        $validator= Validator::make($req->all(),
           [ 'name' =>'required|max:200',
            'email' =>'required|email|max:191|unique:users,email',
            'password' =>'required',
            ]);
             
        if($validator->fails()){
       return response()->json([
           'validation_error'=>$validator->messages(),
       ]);
        } else{
         $company =new Company;
        $user =new User;
        $company->name =$req->input('name');
        $company->email =$req->input('email');
        $company->phone_number =$req->input('phone_number');
        $company->category_id =$req->input('category_id');
        $company->logo=$req->input('logo');
        $company->address_id =$req->input('address_id');
        $company->description=$req->input('description');
        $company->save();

        $user->name=$req->input('name');
        $user->company_id=$req->input('company_id');
        $user->role_id=$req->input('role_id');
        $user->email =$req->input('email');
        $user->password =Hash::make($req->input('password'));
        $user->save();
        return  response()->json(['message'=>'Successfully Created user'],201);; 
      
    }}

   // register json 
    // {
    //     "name":"beauty1",
    //     "companyID":"1",
    //     "categoryID":"1",
    //     "roleID":"1",
    //     "addressID":"1",
    //     "email":"aghmaaaaaa@yahoo.com",
    //     "password":"123456",
    //     "phoneNumber":"0599932123"
    //     }


   














    }