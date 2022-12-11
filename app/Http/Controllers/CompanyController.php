<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'name' =>'required|max:200',
            'email' =>'required|email|max:191|unique:users,email',
            'password' =>'required',
            'category_id'=>'required|exists:categories',
        ]);



        if($validator->fails()){
            return response()->json([
                'validation_error'=>$validator->messages(),
            ]);
            }else{
        $company =Company::create([
            $address=AddressController::createAddress($req->street,$req->city,$req->country),
            'name'=> $req->name,
            'email'=>$req->email,
            'phone_number'=>$req->phone_number,
            'category_id'=>$category->id,
            'logo'=>$req->logo,
            'description'=>$req->description,
            'type'=>$req->type,
            'address_id'=>$address->id//error
        ]);
        $user =User::create([
            $role=Role::where('name','admin')->first(),
            'name'=>$req->name,
            'email'=>$req->email,
            'company_id'=>Company::selectRaw(id)->where('email',$req->email),
            'role_id'=>$role->id,
            'password'=>Hash::make($req->password),

        ]); 
        
        }
    
    }

    // {
    //     "name":"beauty77",
    //     "category_id":"1",
    //     "role_id":"1",
    //     "street":"qwe122",
    //     "city":"jenin",
    //     "country":"palestine",
    //     "email":"aghmaaaamaa@yahoo.com",
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

        return [$company,$address];
        }

        //to do  //need testing
        function updateDetails(Request $req, $id)
        { $company=Company::find($id);
        $company->name =$req->input('name');
        $company->email =$req->input('email');
        $company->phone_number =$req->input('phone_number');
        $company->category_id =$req->input('category_id');
        $company->logo=$req->input('logo');
        $address_id=AddressController::updateAddress($id,$req->input('street'),$req->input('city'),$req->input('country'));
        $company->address_id=(int)$address_id;
        $company->description=$req->input('description');
        $company->type=$req->input('type');
        $company->update();
        return $company;

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



 














    