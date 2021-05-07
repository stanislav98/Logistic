<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->group(function(){
//  	Route::get('/user', function (Request $request) {
// 	    return $request->user();
// 	});
// });
Route::middleware('auth:api')->group(function(){
 	Route::get('/user', function (Request $request) {
	    return $request->user();
	});
	Route::get('tovari','App\Http\Controllers\TovariController@getTovari');

	//prefix for user api
	Route::put('user/{id}','App\Http\Controllers\UserController@updateUser');
	Route::get('users','App\Http\Controllers\UserController@getAllUsers');
    Route::post('users','App\Http\Controllers\UserController@getFilteredUsers');
});

// Route::middleware('auth:sanctum')->get('/authenticated', function (Request $request) {
//     return true;
// });

//PROTECTED ROUTES
// Route::group(['middleware' => 'auth:sanctum'],function(){
// 	Route::get('tovari','App\Http\Controllers\TovariController@getTovari');
// });

//this return authenticated user for broadcasting wtf ?

Route::post('register','App\Http\Controllers\RegisterController@register');
Route::post('nextstep','App\Http\Controllers\NextStepValidator@nextstep');
Route::post('login','App\Http\Controllers\LoginController@login')->name('login');
Route::post('logout','App\Http\Controllers\LoginController@logout');

// VEHICLES
Route::get('vehicles','App\Http\Controllers\VehiclesController@getVehicles');
// Route::put('vehicles','App\Http\Controllers\VehiclesController@getVehicles'); // update name ?
// Route::post('vehicles','App\Http\Controllers\VehiclesController@getVehicles'); // create new one
// Route::delete('vehicles','App\Http\Controllers\VehiclesController@getVehicles'); // delete vehicle

//VEHICLESBYCOMPANY 
Route::get('vehicles/{id}','App\Http\Controllers\VehiclesController@getVehiclesByCompany');
Route::put('vehicles/{id}','App\Http\Controllers\VehiclesController@updateVehiclesByCompany');

//prefix for firm api





Route::post('reset-password', 'App\Http\Controllers\ForgotPassword@sendResetLinkEmail')->name('password.email');
Route::post('reset-password/{token}', 'App\Http\Controllers\ResetPassword@reset');

// Route::middleware('auth:sanctum')->post('/login','App\Http\Controllers\LoginController@login');


//STRIPE
Route::get('subscribe', 'App\Http\Controllers\PaymentController@retrievePlans');
Route::post('subscribe', 'App\Http\Controllers\PaymentController@processSubscription');
Route::post('unsubscribe', 'App\Http\Controllers\PaymentController@endSubscription');
Route::get('invoices/{id}', 'App\Http\Controllers\PaymentController@getInvoices');

//Tovari
Route::post('tovari','App\Http\Controllers\TovariController@insertTovari');

//Transport
Route::get('transport','App\Http\Controllers\TransportController@getTransport');
Route::post('transport','App\Http\Controllers\TransportController@insertTransport');

//Get Different Types
Route::get('transport-tovari-types','App\Http\Controllers\TovariTransportTypes@getTypes');


//Messages

Route::get('private-messages/{id}/{reciever_id}', 'App\Http\Controllers\MessagesController@privateMessages');
Route::post('private-messages/{id}', 'App\Http\Controllers\MessagesController@sendPrivateMessage');


Route::post('/broadcast',function (Request $request){
    // dump($request);
    // dump($request->user_id);
    // dump($request->get("user_id"));
    // dd($request->$request);
    $pusher = new Pusher\Pusher(env('PUSHER_APP_KEY'),env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));
    $auth = $pusher->presence_auth(
    	$request->request->get('channel_name'),
    	$request->request->get('socket_id'),
    	$request->request->get('user_id'),
    	[  
            "id" => $request->request->get('user_id'),
    		"name" => $request->request->get('name'),
    		"firm_name" => $request->request->get('firm_name'),
            "status" => "1"
    	]
	);
    return $auth;
});