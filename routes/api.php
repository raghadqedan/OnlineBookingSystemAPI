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
use App\Http\Controllers\ControlQueues;
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
            Route::get('service/getAllServices',[ServiceController::class,'getAllServices']);//valid
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
            Route::put('setQueueOffDay',[TimeController::class,'setQueueOffDay']);//valid


             // Route::post('createAppointment/{time_id}',[AppointmentController::class,'createAppointment']);// valid
            // Route::put('getTimes',[TimeController::class,'getTimes']);//valid
        });




Route::group(['middleware'=>['auth:sanctum','employee']],function () {

            Route::get('getCurrentCustomer/{queue_id}',[ControlQueues::class,'getCurrentCustomer']);//valid
            Route::post('turnCustomer/{booking_id}/{destination_service_id}',[ControlQueues::class,'turnCustomer']);// valid
            Route::post('CheckOut/{booking_id}',[ControlQueues::class,'CheckOut']);//valid
            Route::put('takeExtraTime/{booking_id}/{dealy_Time}',[ControlQueues::class,'takeExtraTime']);//valid
        });


Route::group(['middleware'=>['auth:sanctum']],function () {

    //users

            Route::get('user/getDetails/{id}',[UserController::class,'getDetails']);//valid
            Route::put('user/updateDetails/{id}',[UserController::class,'updateDetails']);//valid

        });




            Route::get('getAllCategories',['App\Http\Controllers\MobileController\CategoryController'::class,'getAllCategories']);//valid

















   //******************************Customer mobile************

        Route::post('customer/signUp',['App\Http\Controllers\MobileController\CustomerController'::class,'signUp']);//valid
        Route::post('customer/login',['App\Http\Controllers\MobileController\CustomerController'::class,'login']);//valid
        Route::get('customer/get/{id}',['App\Http\Controllers\MobileController\CustomerController'::class,'getCustomer']);//valid
        Route::put('customer/updateProfile/{id}',['App\Http\Controllers\MobileController\CustomerController'::class,'updateProfile']);//valid
        Route::post('customer/editPassword/{id}',['App\Http\Controllers\MobileController\CustomerController'::class,'editPassword']);//not valid
        Route::post('getImage',[ImageController::class,'getImage']);//valid

        Route::get('getLimitCategories',['App\Http\Controllers\MobileController\CategoryController'::class,'getLimitCategories']);//valid

        Route::post('customer/createBooking',['App\Http\Controllers\MobileController\BookingrController'::class,'createBooking']);//valid
        Route::get('customer/getallbooking/{customer_id}', ['App\Http\Controllers\MobileController\BookingrController'::class, 'getAllBooking']);
        Route::get('customer/getbooking/{id}', ['App\Http\Controllers\MobileController\BookingrController'::class, 'getBooking']);

        Route::get('customer/getallservices/{company_id}', ['App\Http\Controllers\MobileController\ServiceController'::class, 'getAllServices']);
        Route::post('customer/searchForService', ['App\Http\Controllers\MobileController\ServiceController'::class, 'searchForService']);
        Route::post('customer/searchForCompany', ['App\Http\Controllers\MobileController\CompanyController'::class, 'searchForCompany']);
        Route::post('customer/searchForCategory', ['App\Http\Controllers\MobileController\CategoryController'::class, 'searchForCategory']);
        Route::get('customer/getServices/{company_id}', ['App\Http\Controllers\MobileController\CustomerController'::class, 'getServices']);
        Route::get('customer/getcompanydetails/{company_id}', ['App\Http\Controllers\MobileController\CustomerController'::class, 'getcompanyDetails']);
        Route::get('customer/getontimes/{company_id}', ['App\Http\Controllers\MobileController\CustomerController'::class, 'getOnTimes']);
        Route::get('customer/getallcompany/{category_id}', ['App\Http\Controllers\MobileController\CompanyController'::class, 'getAllCompany']);




































//days
// Route::post('createDay/{source_id}',[DayController::class,'createDay']);
// Route::get('getOffDays/{source_id}/{type}',[DayController::class,'getOffDays']);
// Route::get('getonDays/{source_id}/{type}',[DayController::class,'getonDays']);
// Route::get('setonDay/{source_id}/{type}/{day}',[DayController::class,'setonDay']);
// Route::get('setOffDay/{source_id}/{type}/{day}',[DayController::class,'setOffDay']);
 //Route::delete('deleteDays/{source_id}/{type})',[DayController::class,'deleteDays']);
// Route::get('isOffDay/{source_id}/{type}/{day}',[DayController::class,'isOffDay']);


// Route::delete('queue/delete/{id}',[QueueController::class,'delete']);//valid



   //times

     //    Route::put('updateTime/{source_id}/{type}/{day}',[TimeController::class,'updateTime']);
     //    Route::post('setEndTime/{source_id}/{type}/{day}/{end_time}',[TimeController::class,'setEndTime']);
     //   Route::post('setStartTime/{source_id}/{type}/{day}/{start_time}',[TimeController::class,'setStartTime']);
      //  Route::get('getEndTime/{source_id}/{type}/{day}',[TimeController::class,'getEndTime']);
     //    Route::get('getStartTime/{source_id}/{type}/{day}',[TimeController::class,'getStartTime']);

 // Route::post('filter1',[CustomerController::class,'filter1']);

