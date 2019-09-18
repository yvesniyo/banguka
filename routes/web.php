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


});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');


