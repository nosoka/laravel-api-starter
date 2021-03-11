<?php

use Illuminate\Support\Facades\Route;

// dingo needs atleast one route to not break routing
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
	$api->post('api', function () { return null; });
});

Route::group(['prefix' => 'api/auth', 'as' => 'api.auth.',  'namespace' => 'Api\Http\Controllers' ], function() {

    Route::post('login', 'AuthController@login')->name('login');
    Route::post('register', 'AuthController@register')->name('register');
    Route::get('verify.email', 'AuthController@verifyEmail')->name('verify.email')->middleware('signed');
    Route::post('send.verification.email', 'AuthController@sendVerificationEmail')->name('send.verification.email');
    Route::post('forgot.password', 'AuthController@forgotPassword')->name('forgot.password');
    Route::post('reset.password', 'AuthController@resetPassword')->name('reset.password');
    Route::get('reset.password', 'AuthController@verifyResetPassword')->name('reset.password');

});
