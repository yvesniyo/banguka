<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
    return view("index");
});

Route::middleware(["auth"])->prefix("admin")->group(function(){
    // Starting  Super Admins
    Route::get("/","AdminController@index")->name("admin.home");
    // Ending  Super Admins



    // Starting Manage Parking Admins
    Route::get("/parkingAdmins","AdminController@getParkingAdmins")->name("getParkingAdmins");
    Route::get("/parkingAdmin/{id}","AdminController@getOneParkingAdmin")->name("getOneParkingAdmin");
    Route::post("/parkingAdmins","AdminController@postParkingAdmins")->name("postParkingAdmins");
    Route::put("/parkingAdmins/{id}","AdminController@updateParkingAdmins")->name("updateParkingAdmins");
    Route::delete("/parkingAdmins/{id}","AdminController@deleteParkingAdmins")->name("deleteParkingAdmins");
    // Ending Manage Parking Admins

    // Starting Manage Parkings Buildings

    Route::get("/parkings","ParkingsController@index")->name("getAllParkings");
    Route::get("/parking/{id}","ParkingsController@getOneParking")->name("getOneParking");
    Route::post("/parkings","ParkingsController@postParkings")->name("postParkings");
    Route::put("/parkings/{id}","ParkingsController@updateParkings")->name("updateParkings");
    Route::delete("/parkings/{id}","ParkingsController@deleteParking")->name("deleteParkings");

    // Ending Manage Parkings Buildings

});

Route::group(["prefix"=>"/parkingAdmin"],function(){

    Route::get("/",function(){
        return "parkingAdmin HomePage";
    })->name("parkingAdmin.home")->middleware("auth");

});

Route::group(["prefix"=>"/parkingAgent"],function(){

    Route::get("/",function(){
        $user = Auth::user();
        return ["Page"=>"parkingAgent HomePage","User"=>$user];
    })->name("parkingAgent.home");

});


Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');


