<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{

    //upload logo
    static function storeImage(Request $req,$foldarName,){
        if($req->hasFile('logo')){
            $file_path =$req->file('logo')->getClientOriginalName();//return the orignal name for photo
            $path=$req->file('logo')->store($foldarName,['disk'=>'public']);
            return $path;

    }}
    static function updateImage(Request $req,$foldarName){

        if($req->hasFile('logo')){
            $file=$req->file('logo');//return the orignal name for photo
            $path=$file->store($foldarName,'public');
            return $path;
    }
    }


    static function getImage(Request $req){

        return  response()->file(public_path( "/storage/".$req->path.""));



    }

    }
