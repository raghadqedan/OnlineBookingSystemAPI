<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Respons;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Company;
use App\Models\Category;
use App\Models\Role;
use App\Http\Controllers\AddressController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
    {
        function register(Request $req)
        {
            $validator=Validator::make($req->all(),[
                'name' =>'required|string|max:200',
                'email' =>'required|email|max:191|unique:users,email',
                'password' =>'required',
                'category_id'=>'required',
            ]);



        if($validator->fails()){

            return response()->json([
                'validation_error'=>$validator->messages(),
            ]);


            }else{
                
                $address=AddressController::createAddress($req->street,$req->city,$req->country);
                $company =Company::create([
                'name'=>$req->name,
                'phone_number'=>$req->phone_number,
                'category_id'=>$req->category_id,
                'logo'=>$req->logo,
                'description'=>$req->description,
                'type'=>$req->type,
                'address_id'=>$address->id,
                 ]);
                $role=Role::where('name','admin')->first();
                
                $user =User::create([
                'role_id'=>$role->id,
                'name'=>$req->name,
                'email'=>$req->email,
                'company_id'=>$company->id, 
                'password'=>Hash::make($req->password),
                
                ]); 

                $token=$user->createToken('myapptoken')->plainTextToken;
                $response=[
                    'user'=>$user,
                    'token'=>$token,
                ];

                 return $response;
                
        
        }
    
    }


 
    // {
    //     "name":"beauty77",
    //     "company_id":"1",
    //     "category_id":"1",
    //     "role_id":"1",
    //     "street":"qwe122",
    //     "city":"jenin",
    //     "country":"palestine",
    //     "email":"aghmaaaaaaa@yahoo.com",
    //     "password":"123456",
    //     "phone_number":"0599932123",
    //     "description":"qqqqqq",
    //     "logo":"image",
    //     "type":"0",
    //     "address_id":"1"
    //         }

    



        function getDetails($id)
            {
                $company=Company::find($id);

                $id=Company::where('id', $id)->get('address_id');

                $address=AddressController::getAddress($id);

                return response()->json([
                    $company,$address
                    ]);
            }

        //to do  //need testing
        function updateDetails(Request $req, $id)
        { $company=Company::find($id);
            
        
        $address=AddressController::updateAddress($company->address_id,$req->street,$req->city,$req->country);
       
        $company->update([
            'name' =>$req->name,
            'email'=>$req->email,
            'phone_number'=>$req->phone_number,
            'category_id'=>$req->category_id,
            'logo'=>$req->logo,
            'description'=>$req->description,
            'type'=>$req->type,
            'address_id'=>$address->id,
           

         ]); 
        return response()->json([$company,$address]);
    }

        
        // {
        //     "name":"beauty77",
        //     "company_id":"1",
        //     "category_id":"1",
        //     "role_id":"1",
        //     "street":"qwe122",
        //     "city":"jenin",
        //     "country":"palestine",
        //     "email":"aghmaaaaaa@yahoo.com",
        //     "password":"123456",
        //     "phone_number":"0599932123",
        //     "description":"qqqqqq",
        //     "logo":"image",
        //     "type":"0",
        //     "address_id":"1"
        //         }
            



        public function delete($id)
        {   
        $result= Company::where('id', $id)->delete();
        if ($result) {
        return ["result"=>"Company account has been delete"];
        } else {
        return ["result"=>"Operation faild"];
        }


        }

        static function getCompanyType($id)
        {   
        return  $company=Company::select('type')->where('id',$id)->get();

        }


        }



 














    