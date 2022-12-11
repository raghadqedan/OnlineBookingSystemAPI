<?php

namespace App\Http\Controllers;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\CompanyController;
class AddressController extends Controller
{  //need testing
   static function getAddress($id){
    $address=Address::find($id);
     return $address;
    }
    static function createAddress($city,$country,$street){
  $address=new Address;
  $address->city=$city;
  $address->country=$country;
  $address->street=$street;
  $address->save();
  $address_id=Address::select('id')->where('city',$city)
    ->where('country',$country)
    ->where('street',$street)->orderBy('created_at', 'desc')->first();
    return  response()->json([
      $address
  ]);

   }
//to do 
   //function to update the address with using the new location from map take location and the company_id as parameter
//   public function resetAddressFromLocation($id,Request $req){
//   $company=Company::find('id');
//   $id=$company->get('address_id');
//   $address=Address::find('id');
//   $address->city=$city;
//   $address->country=$country;
//   $address->street=$street;
//   $address->update();
//     return $address;
//    } 
   //need testing
//function take the address id and the new value of address as parammater to update this id with this value
   static function updateAddress($id,$city,$country,$street){
    
    $address=Address::find($id);
    $address->city=$city;
    $address->country=$country;
    $address->street=$street;
    $address_id=Address::select('id')->where('city',$city)
    ->where('country',$country)
    ->where('street',$street)->orderBy('updated_at', 'desc')->first();
    return $address->$id;//exist error to handel;
   }
    
   }

