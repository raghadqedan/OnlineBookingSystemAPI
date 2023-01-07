<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Service_QueueController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookingController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
       // Route::post('filter1',[CustomerController::class,'filter1']);
            Route::post('register',[CompanyController::class,'register']);//valid
            Route::post('user/login',[UserController::class,'login'])->name('login');//valid



Route::group(['middleware'=>['auth:sanctum','admin']],function () {

   //companies
            Route::get('company/getProfile/{id}',[CompanyController::class,'getDetails']);//valid
            Route::post('company/updateProfile',[CompanyController::class,'updateDetails']);//valid
            Route::delete('company/delete',[CompanyController::class,'deleteCompany']);//valid
            Route::get('getCompanyType',[CompanyController::class,'getCompanyType']);//valid
            //Route::post('resetAddressFromLocation/{id}',[AddressController::class,'resetAddressFromLocation']);



   //services
            Route::get('service/getAllServices',[ServiceController::class,'getAllServices']);//not valid
            Route::get('service/getDetails/{id}',[ServiceController::class,'getDetails']);//valid
            Route::post('service/updateDetails/{id}',[ServiceController::class,'updateDetails']);//valid
            Route::post('service/add',[ServiceController::class,'addService']);//valid
            Route::delete('service/delete/{id}',[ServiceController::class,'delete']);//valid

  //users
            Route::get('user/getAllUsers',[UserController::class,'getAllUsers']);//valid
            Route::post('user/add',[UserController::class,'addUser']);//valid
            Route::delete('user/delete/{id}',[UserController::class,'deleteUser']);//valid
            Route::get('user/getUsers',[UserController::class,'getUsers']);//valid this api get all users in the auth company

  //Queues
            Route::get('queue/getAllQueue',[QueueController::class,'getAllQueue']);// valid
            Route::get('queue/getDetails/{id}',[QueueController::class,'getDetails']);//valid
            Route::post('queue/add',[QueueController::class,'addQueue']);//valid
            Route::put('queue/updateDetails/{id}',[QueueController::class,'updateDetails']);//valid
            Route::delete('queue/delete/{id}',[QueueController::class,'deleteQueue']);//valid



  //Times
           // Route::post('createTime',[TimeController::class,'createTime']);// valid
            Route::put('updateQueueTime',[TimeController::class,'updateQueueTime']);// valid
            Route::put('updateUserTime',[TimeController::class,'updateUserTime']);// valid
            Route::put('updateCompanyTime',[TimeController::class,'updateCompanyTime']);// valid
            Route::get('getscheduleTime/{source_id}/{type}',[TimeController::class,'getscheduleTime']); //valid get schedule times for the source_id (return array )
            Route::put('setUserOffDay',[TimeController::class,'setUserOffDay']);//valid
            Route::put('setUserOnDay',[TimeController::class,'setUserOnDay']);//valid
            Route::put('setCompanyOffDay',[TimeController::class,'setCompanyOffDay']);//valid
            Route::put('setCompanyOnDay',[TimeController::class,'setCompanyOnDay']);//valid
            Route::put('setQueueOnDay',[TimeController::class,'setQueueOnDay']);//valid
            Route::put('setQueueOffDay',[TimeController::class,'setQueueOffDay']);//


        // Route::post('createAppointment/{time_id}',[AppointmentController::class,'createAppointment']);// valid
    // Route::put('getTimes',[TimeController::class,'getTimes']);//valid
        });




Route::group(['middleware'=>['auth:sanctum']],function () {

   //users

            Route::get('user/getDetails/{id}',[UserController::class,'getDetails']);//valid
            Route::put('user/updateDetails/{id}',[UserController::class,'updateDetails']);//valid
            //Route::delete('user/deleteSelected',[UserController::class,'deleteSelected']);
        });










   //times

     //    Route::put('updateTime/{source_id}/{type}/{day}',[TimeController::class,'updateTime']);
     //    Route::post('setEndTime/{source_id}/{type}/{day}/{end_time}',[TimeController::class,'setEndTime']);
     //   Route::post('setStartTime/{source_id}/{type}/{day}/{start_time}',[TimeController::class,'setStartTime']);
      //  Route::get('getEndTime/{source_id}/{type}/{day}',[TimeController::class,'getEndTime']);
     //    Route::get('getStartTime/{source_id}/{type}/{day}',[TimeController::class,'getStartTime']);








   //Customer mobile

            Route::post('customer/signUp',[CustomerController::class,'signUp']);//valid
            Route::get('getAllCategories',[CategoryController::class,'getAllCategories']);//valid
            Route::get('getLimitCategories',[CategoryController::class,'getLimitCategories']);//valid
            Route::post('customer/login',[CustomerController::class,'login']);//valid
            Route::get('customer/get/{id}',[CustomerController::class,'getCustomer']);//valid
            Route::put('customer/updateProfile/{id}',[CustomerController::class,'updateProfile']);//valid
            Route::post('customer/editPassword/{id}',[CustomerController::class,'editPassword']);//not valid
            Route::post('getImage',[ImageController::class,'getImage']);//valid
            Route::post('customer/createBooking',[BookingController::class,'createBooking']);//valid
            Route::get('getbooking/{id}',[BookingController::class,'getBooking']);//valid
// Route::group(['middleware'=>['auth:sanctum','employee']],function () {




// });

















//days
// Route::post('createDay/{source_id}',[DayController::class,'createDay']);
// Route::get('getOffDays/{source_id}/{type}',[DayController::class,'getOffDays']);
// Route::get('getonDays/{source_id}/{type}',[DayController::class,'getonDays']);
// Route::get('setonDay/{source_id}/{type}/{day}',[DayController::class,'setonDay']);
// Route::get('setOffDay/{source_id}/{type}/{day}',[DayController::class,'setOffDay']);
 //Route::delete('deleteDays/{source_id}/{type})',[DayController::class,'deleteDays']);
// Route::get('isOffDay/{source_id}/{type}/{day}',[DayController::class,'isOffDay']);


// Route::delete('queue/delete/{id}',[QueueController::class,'delete']);//valid
