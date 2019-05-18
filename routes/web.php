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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

URL::forceRootUrl(getenv("APP_URL"));

Auth::routes(['register' => false]);

Route::get("/blockedByPShield", "PShieldController@banPage")->name("pshieldban");

Route::get("/admin", "PShieldController@banIP");
Route::get("/wp-admin", "PShieldController@banIP");

Route::get('/', function () {
    return view('landing');
});

Route::group(['middleware' => 'auth'], function () {

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get("/home/lehrer/search/", "TimeController@lehrer_time_table");

    Route::post("/home/lehrer/request", "TimeController@requestDate");

    Route::get("/home/schueler/requestList", function () {
        return view("layouts.schueler_requests");
    });

    Route::post("/home/lehrer/cancelRequestS", "TimeController@schuelerCancelRequest");

    Route::get("/home/lehrer/terminplan", "TimeController@lehrerTerminPlan");

    Route::post("/home/lehrer/acceptRequest", "TimeController@acceptRequestLehrer");

    Route::post("/home/lehrer/denyRequest", "TimeController@denyRequestLehrer");

    Route::get("/home/lehrer/dashboard", function () {
        return view("layouts.lehrer_dashboard");
    });

    Route::get("/home/chat", "ChatController@chatWindow");
});
