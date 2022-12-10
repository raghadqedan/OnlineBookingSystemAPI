<?php

namespace App\Http\Controllers;
use App\Models\Day;
use Illuminate\Http\Request;

// class DayController extends Controller
// {


//     function createDay(Request $req ,$source_id)
//     {  
//             $obj= new Day;
//             $obj->source_id=$source_id;  
//             $obj->day=$req->input('day');
//             $obj->status=$req->input('status');
//             $obj->save();
//             return $obj;
//   }
//     // createDay json file 
//     // {"day":"1",
//     //     "status":"0"
//     //     }
        



    
// //get all offdays for any take source_id , id_source,status,type as parameter.
//     function getOffDays($source_id,$type)
//     {
//         $offDays=Day::selectRaw('day')
//         ->where('source_id',$source_id)->where('type',$type)->where('status',false)->get();
//         return $offDays;
//     }

//     function getonDays($source_id,$type)
//     {
//         $onDays= Day::selectRaw('day')
//        ->where('source_id',$source_id)
//        ->where('type',$type)
//        ->where('status',true)
//        ->get();
//         return $onDays;
//     }
//     function setOffDay($source_id,$type,$day )
//     {
//         $obj= Day::where('source_id',$source_id)->where('type',$type)->where('day',$day)->get();
//         $obj->toQuery()->update(['status'=>false,]);
//         return  "successfully updated";
//     }

//     function setonDay($source_id,$type,$day)
//     {
//         $obj=Day::where('source_id',$source_id)->where('type',$type)->where('day',$day)->get();
//         $obj->toQuery()->update(['status'=>true,]);
//         return  "successfully updated";
//     }

//     function isOffDay($source_id,$type,$day)
//     { 
//         $result= Day::where('source_id',$source_id)->where('type',$type)->where('day',$day)->get();
       
//         return ($result->toQuery()->get('status'));
//     }

  


    
// //delet oll off days for the determine id ,used when clear the component from the database
    // function deleteDays($source_id, $type)
    // {
    //     $result= Day::where('source_id',$source_id)->where('type',$type)->delet();
    //    while($result)
    //        $result->toQuery()->delet();
        
    //     if ($result){
    //         return ["result"=>"OffDays has been delete"];
    //      }
    //      else{
    //          return ["result"=>"Operation faild"];
  
    //      }
    // }

//function take sourceid of the queue or company or service or user ,day and return true if this day is offday .
//to do 
 
    
// }
