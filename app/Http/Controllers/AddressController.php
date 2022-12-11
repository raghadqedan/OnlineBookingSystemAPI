<?php


namespace App\Http\Controllers;
 use Illuminate\Http\Response;
 use Illuminate\Http\JsonResponse;
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
      $address=Address::create([
        'city'=>$city,
        'country'=>$country,
        'street'=>$street,
      ]);

      return $address;}

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
    return $address;
   

   }
    
   }
