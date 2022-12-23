<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

use Illuminate\Support\Facades\Validator;

        class ServiceController extends Controller
        {

                function getDetails($id)
                {
                $service=Service::find($id);
                return $service;
                }


                public function addService(request $req)
                {
                        $validator=Validator::make($req->all(),[
                        'name' =>'required|string|max:200',
                        'duration_time' =>'required',
                        'logo'=>'required',
                                ]);



                        if($validator->fails()){

                                return response()->json([
                                'validation_error'=>$validator->messages(),
                                ]);


                        }else{

                                $service=Service::create([
                                'name'=>$req->name,
                                'duration_time'=>$req->duration_time,
                                'logo'=>$req->logo
                                ]);
                                return response()->json([$service ]);
                        }
                }

                // {
                //         "name":"makeup",
                //         "duration_time":"1",
                //         "logo":"image"
                //  }







                function updateDetails(Request $req, $id)
                { $service= Service::find($id);
                        $service->update([
                        'name' =>$req->name,
                        'logo'=>$req->logo,
                        'duration_time'=>$req->duration_time,
                         ]);
                        return response()->json([$service]);
                }





                public function delete($id)
                {
                $result= Service::where('id', $id)->delete();
                if ($result) {
                return ["result"=>"service has been delete"];
                } else {
                return ["result"=>"Operation faild"];
                }
                }



                static function getServiceDurationTime($id)
                { $duration_time= Service::selectRaw('duration_time')->where('id',$id);

                return $duration_time;

                }




}
