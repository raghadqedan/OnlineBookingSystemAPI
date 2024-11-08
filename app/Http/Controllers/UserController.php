<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Time;
use App\Models\Queue;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use Nette\Utils\ArrayList;
use PhpParser\Node\Expr\List_;
use SebastianBergmann\CodeCoverage\Node\Iterator;
use Traversable;
class UserController extends Controller
{



            function getDetails($id){
                $user=User::find($id);
                return $user;

            }


            function getAllUsers()
            {
                $users=User::where('company_id',auth()->user()->company_id)->get();
                return response()->json(['users'=>$users]);
            }





            function updateDetails(Request $req, $id){

                $user= User::find($id);
                $user->name=$req->input('name');
                $user->email=$req->input('email');
                $user->password=Hash::make($req->input('password'));
                $user->phone_number=$req->input('phone_number');
                $user->role_id=$req->input('role_id');

                $user->update();

                return $user;

            }

            //   {
            //    "name":"rama",
            //     "role_id":"2",
            //     "email":"qqqqqqq@yahoo.com",
            //     "password":"18888887890",
            //     "phone_number":"222222222",
            //     }



            function login(Request $req){

                $user= User::where('email',$req->email)->first();
                if(!$user||!Hash::check($req->password,$user->password))
                    return response()->json([
                    'status'=>401,
                    'message'=>'Invalid Credentials'

                ]);

                else{
                    $token=$user->createToken('myapptoken')->plainTextToken;
                    $response=[
                        'status'=>200,
                        'message'=>'valid Credentials',
                        'user'=>$user,
                        'token'=>$token,
                        ]; }
                return $response;

            }



            function deleteSelected(ArrayList $id){

                for($i=0;$i<sizeof($id);$i++)
                    $result= User::where('id', $id[$i])->delete();
                    if ($result){
                        return ["result"=>"selected users have been delete"];
                    } else {
                        return ["result"=>"Operation faild"];
                    }
                }




            public function addUser(request $req){
                $validator=Validator::make($req->all(),[
                'name' =>'required',
                'email' =>'required|email|max:191|unique:users,email',
                'password' =>'required',
                'role_id' =>'required',
                'phone_number'=>'required',
                ]);

                if($validator->fails()){
                    return response()->json([
                        'validation_error'=>$validator->messages(),
                    ]);}

                else{
                $user= new User();
                $user->name =$req->input('name');
                $user->email =$req->input('email');
                $user->password =Hash::make($req->input('password'));
                $user->role_id =$req->input('role_id');
                $user->company_id =auth()->user()->company_id;
                $user->phone_number =$req->input('phone_number');
                $user->save();
                //create   default scheduleTimes for the user default start,end times  value from companytimes.

                    for($i=0;$i<7;$i++){
                    $company=Time::where('source_id', $user->company_id)->where('day',$i)->where('type',"0")->first();
                            if($company->status=="1"){

                                $request = new Request([
                                    'day'=>$i,
                                    'type'=>"1",
                                    'source_id'=>$user->id,
                                    'start_time'=>$company->start_time,
                                    'end_time'=>$company->end_time,
                                    'status'=>"1"
                                ]);
                            }else{
                                $request = new Request([
                                    'day'=>$i,
                                    'type'=>"1",
                                    'source_id'=>$user->id,
                                    'start_time'=>$company->start_time,
                                    'end_time'=>$company->end_time,
                                    'status'=>"0"
                                ]);
                            }

                    TimeController::createTime( $request);
                    }

                return $user;
            }
        }









            static function getCompanyType(){
                    return  auth()->user()->company_id;

            }






            public function getUsers(){
                    $user=User::where('id',auth()->user()->company_id)->get();
                    return $user;

            }



            static function deleteUser($user_id){

                $user= User::where('id',$user_id)->where('status',1)->first();

                if($user){

                    $obj=Time::where('source_id',$user_id)->where('type',1)->whereIn('status', [0,1])->get();//return all children times for this queue

                    if($obj){//update all times for this user
                            foreach($obj as $time){
                                        $time->update(['status'=>-1]);

                                }
                    }
                    $queues=Queue::where('user_id',$user_id)->whereIn('active', [0,1])->get();//return all children queue

                if($queues){//update all queues for this user
                    foreach($queues as $q){

                                QueueController::deleteQueue($q->id);
                        }
                    }

                    $user->update(['status'=>-1]);
                    return  response()->json([ 'message'=>'user deleted successfully' ]);
                }
                return  response()->json([ 'message'=>'opration failed ,This user does not exist' ]);
            }















}












    // {"name":"beauty",
    //     "company_id":"1",
    //     "category_id":"1",
    //     "role_id":"1",
    //      "address_id":"1",
    //     "email":"rrr@yahoo.com",
    //     "password":"1234567890",
    //     "phone_number":"0599932123"}


    // public function delete($id){
    //     $result= User::where('id', $id)->delete();
    //     if ($result) {
    //         return ["result"=>"user has been delete"];
    //     } else {
    //         return ["result"=>"Operation faild"];
    //     }
    // }
