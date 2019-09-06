<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('/login', 'UsersController@login');
Route::post('/register', 'UsersController@register');
Route::get("/logout","UsersController@logout");

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('details', 'UsersController@details');
});

Route::get('/error/{status}', function($status){
    return ["status"=>$status, "message"=>"Unauthorized Request"];
})->name("errorHandleUnauthorized");
