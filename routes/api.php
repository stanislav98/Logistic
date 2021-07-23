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

Route::post('contact','App\Http\Controllers\ContactController@sendMessage')->middleware('xssSanitizer');

Route::middleware('auth:api')->group(function(){
 	Route::get('/user', function (Request $request) {
	    return $request->user();
	});

    //Tovari EndPoints
	Route::get('tovari','App\Http\Controllers\TovariController@getTovari');
    Route::post('tovari','App\Http\Controllers\TovariController@insertTovariTransport')->middleware('xssSanitizer');
    Route::delete('tovari/{id}','App\Http\Controllers\TovariController@deleteTovar');
    Route::put('tovari/{id}','App\Http\Controllers\TovariController@updateTovarTransport')->middleware('xssSanitizer');
    Route::get('relatedtovari/{id}','App\Http\Controllers\TovariController@getTovariByFirm');

    //Transport EndPoints
    Route::get('transport','App\Http\Controllers\TransportController@getTransport');
    Route::post('transport','App\Http\Controllers\TransportController@insertTransportTransport')->middleware('xssSanitizer');
    Route::delete('transport/{id}','App\Http\Controllers\TransportController@deleteTransport');
    Route::put('transport/{id}','App\Http\Controllers\TransportController@updateTovarTransport')->middleware('xssSanitizer');
    Route::get('relatedtransport/{id}','App\Http\Controllers\TransportController@getTransportByFirm');

	//Users EndPoints
	Route::put('user/{id}','App\Http\Controllers\UserController@updateUser'); // should have middleware to check if is admin or is curr user or is owner of user
    Route::get('user/{id}','App\Http\Controllers\UserController@getUserById');
    Route::get('relatedusers/{id}','App\Http\Controllers\UserController@getRelatedUsers');
	Route::get('users','App\Http\Controllers\UserController@getAllUsers');
    Route::post('users','App\Http\Controllers\UserController@getFilteredUsers');
    Route::get('users/unauthorized','App\Http\Controllers\UserController@getUnauthorizedUsers');
    Route::put('users/unauthorized','App\Http\Controllers\UserController@authorizeUser');
    Route::post('add-users','App\Http\Controllers\UserController@addUser');
    Route::delete('users/{id}','App\Http\Controllers\UserController@deleteUser'); // should have middleware to check if is admin or is curr user or is owner of user
    
    Route::post('logout','App\Http\Controllers\LoginController@logout');


    //VEHICLESBYCOMPANY 
    Route::get('vehicles/{id}','App\Http\Controllers\VehiclesController@getVehiclesByCompany');
    Route::put('vehicles/{id}','App\Http\Controllers\VehiclesController@updateVehiclesByCompany');

    //prefix for firm api

    //Firms
    Route::get('firms','App\Http\Controllers\FirmController@getFirms');
    Route::get('firms/{id}','App\Http\Controllers\FirmController@getFirmById');

    //STRIPE
    Route::get('subscribe', 'App\Http\Controllers\PaymentController@retrievePlans');
    Route::post('subscribe', 'App\Http\Controllers\PaymentController@processSubscription');
    Route::post('unsubscribe', 'App\Http\Controllers\PaymentController@endSubscription');
    Route::get('invoices/{id}', 'App\Http\Controllers\PaymentController@getInvoices');

    //Transport
    Route::get('transport','App\Http\Controllers\TransportController@getTransport');
    Route::post('transport','App\Http\Controllers\TransportController@insertTransport');

    //Rating
    Route::get('/rating/{id}','App\Http\Controllers\RatingController@getSingleRating');
    Route::post('/rating','App\Http\Controllers\RatingController@setRating');

    //Comments
    Route::get('/comments/{id}','App\Http\Controllers\CommentController@getCommentsById')->middleware('auth:api');
    Route::post('/comments','App\Http\Controllers\CommentController@setComment')->middleware('auth:api','xssSanitizer');
    Route::put('/comments/{id}', 'App\Http\Controllers\CommentController@update')->middleware('auth:api','xssSanitizer');
    Route::delete('/comments/{id}', 'App\Http\Controllers\CommentController@destroy')->middleware('auth:api');

    //Reports
    Route::get('/reports/{id}','App\Http\Controllers\ReportController@getReportsById')->middleware('auth:api');
    Route::post('/reports','App\Http\Controllers\ReportController@setReport')->middleware('auth:api','xssSanitizer');
    Route::post('/reports/update/{id}', 'App\Http\Controllers\ReportController@update')->middleware('auth:api','xssSanitizer');
    Route::delete('/reports/{id}', 'App\Http\Controllers\ReportController@destroy')->middleware('auth:api');
    Route::put('/reports/{id}', 'App\Http\Controllers\ReportController@approve')->middleware('auth:api');

    //Get Different Types
    Route::get('transport-tovari-types','App\Http\Controllers\TovariTransportTypes@getTypes');


    //Messages

    Route::get('private-messages/{id}', 'App\Http\Controllers\MessagesController@privateMessages');
    Route::post('private-messages/', 'App\Http\Controllers\MessagesController@sendPrivateMessage');

    //Notifications

    Route::get('/notifications/{id}','App\Http\Controllers\NotificationsController@getNotifications');
    Route::post('/notifications','App\Http\Controllers\NotificationsController@updateNotifications');

});


Route::post('register','App\Http\Controllers\RegisterController@register');
Route::post('nextstep','App\Http\Controllers\NextStepValidator@nextstep');
Route::post('login','App\Http\Controllers\LoginController@login')->name('login');

// VEHICLES
// Route::put('vehicles','App\Http\Controllers\VehiclesController@getVehicles'); // update name ?
// Route::post('vehicles','App\Http\Controllers\VehiclesController@getVehicles'); // create new one
// Route::delete('vehicles','App\Http\Controllers\VehiclesController@getVehicles'); // delete vehicle





Route::post('reset-password', 'App\Http\Controllers\ForgotPassword@sendResetLinkEmail')->name('password.email');
Route::post('reset-password/{token}', 'App\Http\Controllers\ResetPassword@reset');

// Route::middleware('auth:sanctum')->post('/login','App\Http\Controllers\LoginController@login');


Route::get('vehicles','App\Http\Controllers\VehiclesController@getVehicles');





Route::post('/broadcast',function (Request $request){
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


