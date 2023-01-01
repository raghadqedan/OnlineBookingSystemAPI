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
use App\Http\Controllers\ImageController;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CompanyController
    {

        function register(Request $req)
        {
            $validator=Validator::make($req->all(),[
                'name' =>'required|string|max:200',
                'email' =>'required|email|max:191|unique:users,email',
                'password' =>'required',
                'category_id'=>'required',
                'type'=>'required',//company type
            ]);



            if($validator->fails()){

                return response()->json([
                    'validation_error'=>$validator->messages(),
                ]);


            }else{

                $lock=1;
                DB::beginTransaction();
                    try{
                        $address=AddressController::createAddress($req->street,$req->city,$req->country);
                        $company=Company::create([
                        'name'=>$req->name,
                        'phone_number'=>$req->phone_number,
                        'category_id'=>$req->category_id,
                        'logo'=>ImageController::storeImage($req,"company"),
                        'description'=>$req->description,
                        'type'=>$req->type,
                        'address_id'=>$address->id,
                        ]);
                        DB::commit();

                        }catch (Exception $e) {

                        return response()->json([
                            "result"=>"Operation faild"
                            ]);
                        $lock=0;
                }


                if($lock){
                    try{
                        $role=Role::where('name','admin')->first();

                        $user =User::create([
                        'role_id'=>$role->id,
                        'name'=>$req->name,
                        'email'=>$req->email,
                        'password'=>Hash::make($req->password),
                        'company_id'=> $company->id,
                        ]);







                    }
                    catch (Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            "result"=>"Operation faild"
                            ]);

                            $lock=0;
                        }}


                if($lock){
                    try{
                     //create  company scheduleTimes for the company wuth  default start,end times  value.
                        for($i=0;$i<7;$i++){

                                $request1 = new Request([
                                    'day'=>$i,
                                    'type'=>"0",
                                    'source_id'=>$company->id,
                                    'start_time'=>"08:00:00",
                                    'end_time'=>"14:00:00",
                                    'status'=>"1"
                                ]);
                                TimeController::createTime( $request1);
                                $request2 = new Request([
                                    'day'=>$i,
                                    'type'=>"1",
                                    'source_id'=>$user->id,
                                    'start_time'=>"08:00:00",
                                    'end_time'=>"14:00:00",
                                    'status'=>"1"

                                ]);
                                TimeController::createTime( $request2);
                        }




                        $token=$user->createToken('myapptoken')->plainTextToken;
                        DB::commit();
                        return  response()->json([
                            "result"=>"company account created successfully",
                            "token"=>$token,
                            "user"=>$user,
                            "company"=>$company,

                            ]);

                    }catch(Exception $e){

                        DB::rollBack();
                        return response()->json([
                            "result"=>"Operation faild"
                            ]);}




        }}}





    // {
    //     "name":"beauty77",
    //     "category_id":"1",
    //     "street":"qwe122",
    //     "city":"tulkarm",
    //     "country":"palestine",
    //     "email":"aghmaaaaaaa@yahoo.com",
    //     "password":"123456",
    //     "phone_number":"0599932123",
    //     "description":"qqqqqq",
    //     "logo":"image",
    //     "type":"0"
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


        function updateDetails(Request $req)
        {
            $company=Company::where('id',auth()->user()->company_id)->first();

            $address=AddressController::updateAddress($company->address_id,$req->street,$req->city,$req->country);

            $company->update([
                'name' =>$req->name,
                'email'=>$req->email,
                'phone_number'=>$req->phone_number,
                'category_id'=>$req->category_id,
                'logo'=>ImageController::updateImage($req,"company"),
                'description'=>$req->description,
                'type'=>$req->type,
                'address_id'=>$address->id,


            ]);
        return response()->json([$company,$address]);
    }


        // {
        //     "name":"beautysalon",
        //     "category_id":"1",
        //     "street":"qwe122",
        //     "city":"tulkarm",
        //     "country":"palestine",
        //     "email":"aghmaaaaaa@yahoo.com",
        //     "password":"123456",
        //     "phone_number":"0599932123",
        //     "description":"qqqqqq",
        //     "logo":"image",
        //     "type":"0",
        //     "address_id":"1"
        //         }




        public function delete()
        {
        $result= Company::where('id', auth()-user()->company_id)->delete();
        if ($result) {
        return ["result"=>"Company account has been delete"];
        } else {
        return ["result"=>"Operation faild"];
        }


        }

        static public function getCompanyType()
        {
            $type=Company::selectRaw('type')->where('id',auth()->user()->company_id)->get();
            return $type;

        }


        }


















