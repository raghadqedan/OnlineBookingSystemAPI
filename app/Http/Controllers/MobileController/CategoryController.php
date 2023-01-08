<?php

namespace App\Http\Controllers\MobileController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
            public function searchForCategory(Request $req)
        {
            $name = $req->name;
            $category = Category::where('name','like',"%$name%")->get();
            return $category;
        }


        function getAllCategories(){

            return Category::all();
        }


        function getLimitCategories(){
            return Category::take(6)->get();
        }
















}
