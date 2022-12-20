<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    function getAllCategories(){
       return Category::all();
 }


 function getLimitCategories(){
    return Category::take(6)->get();
}






}
