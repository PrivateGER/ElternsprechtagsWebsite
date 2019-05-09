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

URL::forceRootUrl(getenv("APP_URL"));

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get("/api/timetable/:lehrer/", 'HomeController@teacher_timetable');

Auth::routes();

Route::get("/home/lehrer/search/", "TimeController@lehrer_time_table");

Auth::routes();

Route::post("/home/lehrer/request", "TimeController@requestDate");

Auth::routes();

Route::get("/home/schueler/requestList", function() {
    return view("layouts.schueler_requests");
});

Auth::routes();

Route::post("/home/lehrer/cancelRequestS", "TimeController@schuelerCancelRequest");

Auth::routes();

Route::get("/home/lehrer/terminplan", "TimeController@lehrerTerminPlan");

Auth::routes();

Route::post("/home/lehrer/acceptRequest", "TimeController@acceptRequestLehrer");

Auth::routes();

Route::post("/home/lehrer/denyRequest", "TimeController@denyRequestLehrer");

Auth::routes();

Route::get("/home/lehrer/dashboard", function () {
    return view("layouts.lehrer_dashboard");
});