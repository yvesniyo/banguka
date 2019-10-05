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


Route::post('/login', 'UsersController@login');
Route::post('/register', 'UsersController@register');
Route::get("/logout","UsersController@logout")->middleware("auth:api");

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('details', 'UsersController@details');

    Route::post('questions/create', 'QuestionsController@create');
    Route::get('questions', 'QuestionsController@showAll');
    Route::get('questions/{offset}/{numbers}','QuestionsController@retrieveInPacket');
    Route::get('questions/max','QuestionsController@maximumQuestions');
    Route::post("questions/update/{id}","QuestionsController@update");
    
    Route::post("image/store","QuestionsController@store");

});

Route::get('/error/{status}', function($status){
    return response()->json(["status"=>401,"message"=>"Unauthorized"], 401);
})->name("errorHandleUnauthorized");
