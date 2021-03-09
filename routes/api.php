<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'auth', 'as' => 'api.auth', 'namespace' => 'Api\Http\Controllers'], function ($api) {

		$api->post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
		$api->post('register', ['as' => 'register', 'uses' => 'AuthController@register']);

		$api->get('verify.email', ['as' => 'verify.email', 'uses' => 'AuthController@verifyEmail'])->middleware('signed');
		$api->post('resend.verification.email', ['as' => 'resend.verification.email', 'uses' => 'AuthController@resendVerificationEmail']);
	});
});
