<?php

namespace App\Http\Controllers\MobileController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
class ServiceController extends Controller
{
    public function searchForService(Request $req)
    {
        $name = $req->name;
        $service = Service::where('name','like',"%$name%")->get();
        return $service;

    }
    public function getAllServices($company_id)
    {

        $service = Service::where('company_id', $company_id)->get();
        return response()->json(['service' => $service]);

    }


}
