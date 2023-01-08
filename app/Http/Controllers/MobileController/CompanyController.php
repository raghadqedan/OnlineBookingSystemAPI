<?php

namespace App\Http\Controllers\MobileController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
class CompanyController extends Controller
{
    public function searchForCompany(Request $req)
               {
                   $name = $req->name;
                   $company = Company::where('name','like',"%$name%")->get();
                   return $company;
               }
}
