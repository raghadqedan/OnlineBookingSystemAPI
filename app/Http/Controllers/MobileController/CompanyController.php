<?php

namespace App\Http\Controllers\MobileController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Time;
class CompanyController extends Controller
{
    public function searchForCompany(Request $req) {
            $name = $req->name;
            $company = Company::where('name','like',"%$name%")->get();
            return  response()->json(['company' => $company]);
                }


                public function getAllCompany($category_id)
                {
                    $company = Company::where('category_id', $category_id)->get();
                    return response()->json(['company' => $company]);

                }

                public function getOnDays($company_id)
                {
                    $time = Time::where('source_id', $company_id)
                        ->where('type', 0)
                        ->where('status', 1)
                        ->selectRaw('day')
                        ->get();

                    $day = array();

                    foreach ($time as $t) {
                        $day[] = jddayofweek($t->day, 1);

                    }
                    return response()->json(['day' => $day]);
                }



}
