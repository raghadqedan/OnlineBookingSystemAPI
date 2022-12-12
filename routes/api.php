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
      
            Route::get('getcompanyprofile/{id}',[CompanyController::class,'getDetails']);//valid
            Route::put('updatecompanyprofile/{id}',[CompanyController::class,'updateDetails']);//valid
            Route::delete('delete/{id}',[CompanyController::class,'delete']);
            //Route::post('resetAddressFromLocation/{id}',[AddressController::class,'resetAddressFromLocation']);

 });


Route::group(['middleware'=>['auth:sanctum']],function () {

            Route::get('user/getDetails/{id}',[UserController::class,'getDetails']);//valid
            Route::put('user/updateDetails/{id}',[UserController::class,'updateDetails']);//valid
            //Route::delete('user/deleteSelected',[UserController::class,'deleteSelected']);
            Route::post('user/addUser',[UserController::class,'addUser']);//valid
            Route::delete('user/delete/{id}',[UserController::class,'delete']);//valid

    });


//times
        Route::post('createTime',[TimeController::class,'createTime']);
        Route::get('getscheduleTime/{source_id}/{type}',[TimeController::class,'getscheduleTime']);
        Route::put('updateTime/{source_id}/{type}/{day}',[TimeController::class,'updateTime']);
        Route::post('setEndTime/{source_id}/{type}/{day}/{end_time}',[TimeController::class,'setEndTime']);
        Route::post('setStartTime/{source_id}/{type}/{day}/{start_time}',[TimeController::class,'setStartTime']);
        Route::get('getEndTime/{source_id}/{type}/{day}',[TimeController::class,'getEndTime']);
        Route::get('getStartTime/{source_id}/{type}/{day}',[TimeController::class,'getStartTime']);

//services
        Route::get('service/getDetails/{id}',[ServiceController::class,'getDetails']);
        Route::put('service/updateDetails/{id}',[ServiceController::class,'updateDetails']);
        Route::post('service/addService',[ServiceController::class,'addService']);
        Route::delete('service/delete/{id}',[ServiceController::class,'delete']);


//Queues

        Route::get('queue/getDetails/{id}',[QueueController::class,'getDetails']);
        Route::put('queue/updateDetails/{id}',[QueueController::class,'updateDetails']);
        Route::post('queue/add',[QueueController::class,'addQueue']);
        Route::delete('queue/delete/{id}',[QueueController::class,'delete']);
        
        //Route::post('getCompanyType/{id}',[CompanyController::class,'getCompanyType']);

//Customer

        Route::post('signUp',[CustomerController::class,'signUp']);//valid

// // Route::get('getAllCategories',[CategoryController::class,'getAllCategories']);

// Route::group(['middleware'=>['auth:sanctum','employee']],function () {




// });

















//days 
// Route::post('createDay/{source_id}',[DayController::class,'createDay']);
// Route::get('getOffDays/{source_id}/{type}',[DayController::class,'getOffDays']);
// Route::get('getonDays/{source_id}/{type}',[DayController::class,'getonDays']);
// Route::get('setonDay/{source_id}/{type}/{day}',[DayController::class,'setonDay']);
// Route::get('setOffDay/{source_id}/{type}/{day}',[DayController::class,'setOffDay']);
// //Route::delete('deleteDays/{source_id}/{type})',[DayController::class,'deleteDays']);
// Route::get('isOffDay/{source_id}/{type}/{day}',[DayController::class,'isOffDay']);