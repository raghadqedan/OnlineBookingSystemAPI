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
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Time;
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
                $category=Category::where('id',$company->category_id)->get();

                return response()->json([
                    "company"=> $company,
                    "address"=>$address,
                    "category"=>$category
                    ]);
            }


        function updateDetails(Request $req)
        {
            $company=Company::where('id',auth()->user()->company_id)->first();

            $address=AddressController::updateAddress($company->address_id,$req->street,$req->city,$req->country);

            $company->update([
                'name' =>$req->name,
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





        static public function getCompanyType()
        {
            $type=Company::selectRaw('type')->where('id',auth()->user()->company_id)->get();
            return $type;

        }




        static function deleteCompany(){

            $company= Company::where('id',auth()->user()->company_id)->where('status',1)->first();

            if($company){

                $obj=Time::where('source_id',$company->id)->where('type',0)->whereIn('status', [0,1])->get();//return all children times for this queue

                if($obj){//update all times for this user
                        foreach($obj as $time){
                                    $time->update(['status'=>-1]);

                            }
                }
                $users=User::where('company_id',$company->id)->where('status',1)->get();//return all children queue

            if($users){//update all queues for this user
                foreach($users as $u){

                            UserController::deleteUser($u->id);
                    }
                }

                $company->update(['status'=>-1]);
                return  response()->json([ 'message'=>'company deleted successfully' ]);
            }
            return  response()->json([ 'message'=>'opration failed ,This company does not exist' ]);
        }


        public function filterClient(Request $req)
        {
            $name=$phone_number=$email="";
            $name =$req->name;
            $phone_number = $req->phone_number;
            $email = $req->email;



            if (!empty($email)) {
                $customer= Customer::where('email', $email)
                ->get();
            } elseif (!empty($name) && !empty($phone_number)) {
                $customer = Customer::where('first_name', 'LIKE', "%{$name}%")
                    ->orwhere('last_name', 'LIKE', "%{$name}%")
                    ->orWhereRaw("concat(first_name,' ', last_name) like '%" . $name . "%' ")
                    ->where('phone_number', $phone_number)
                    ->get();
            }

            return response()->json(['customer'=>$customer]);
        }




        public function filterEmployee(Request $req)
        {
            $name=$phone_number=$email="";
            $name =$req->name;
            $phone_number = $req->phone_number;
            $email = $req->email;
            $role = $req->role;
            $id = $req->id;

            if (!empty($email)) {
                $user= User::where('email', $email)
                ->get();
            } elseif (!empty($name)) {
                $user = User::where('name', $name)
                    ->get();
            } elseif (!empty($phone_number)) {
                $user = User::where('phone_number', $phone_number)
                    ->get();
            } elseif (!empty($name) && !empty($phone_number)) {
                $user = User::where('name', $name)
                    ->where('phone_number', $phone_number)
                    ->get();
            } elseif (!empty($role)) {
                $role = Role::where('name', $role)->first();
                $user= User::where('role_id', $role->id)->get();
            }

            return response()->json(['user'=>$user]);
        }
















        }


















