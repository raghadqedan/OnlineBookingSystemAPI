<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

        class ServiceController extends Controller
        {
        function getDetails($id)
        {
        $service=Service::find($id);

        return $service;
        }


        public function addService(request $req)
        {
        $service= new Service();
        $service->name=$req->input('name');
        $service->duration_time=$req->input('duration_time');
        $service->logo=$req->input('logo');
        $service->save();

        return $service;
        }



        public function delete($id)
        {
        $result= Service::where('id', $id)->delete();
        if ($result) {
        return ["result"=>"user has been delete"];
        } else {
        return ["result"=>"Operation faild"];
        }
        }


        function updateDetails(Request $req, $id)
        { $service= Service::find($id);
        $service->name=$req->input('name');
        $service->duration_time=$req->input('duration_time');
        $service->logo=$req->input('logo');
        $service->update();
        return $service;

        }

        static function getServiceDurationTime($id)
        { $duration_time= Service::selectRaw('duration_time')->where('id',$id);

        return $duration_time;

        }




}
