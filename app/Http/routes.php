<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', function () {
//   return view('welcome');
//});

Route::auth();
Route::get('/', function() {
	return redirect('/dashboard');
});

Route::get('/home', function() {
	return redirect('/dashboard');
});

//Dashboard
Route::get('/dashboard', ['middleware' => 'auth', 'uses' => 'HomeController@index']);

//Searches
Route::get('/queue', 'HomeController@new_queue');
Route::get('/queue/delete', 'HomeController@delete_queue');
Route::get('/queue/reset', 'HomeController@reset_queue');
Route::post('/queue/save', 'HomeController@save_queue');
Route::post('/queue/update', 'HomeController@update_queue');

//Profile
Route::get('/profile/view', 'HomeController@profile_view');
Route::get('/profile/sendcode', 'HomeController@send_code');
Route::post('/profile/save', 'HomeController@profile_save');

//Admin 
Route::get('/user/list', 'HomeController@user_list');
Route::post('/user/save', 'HomeController@user_save');
Route::get('/user/loginas', 'HomeController@user_loginas');
