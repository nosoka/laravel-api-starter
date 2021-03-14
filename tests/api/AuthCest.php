<?php

use Api\Models\User;
use Faker\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Step\Api\AuthStep;

class AuthCest
{
    protected $faker;
    protected $user;

    // list actions here that you want to execute before each test
    public function _before(ApiTester $I, User $user)
    {
        $this->faker = Factory::create();
        $this->user  = $user;

        $this->_verifyEnvironment($I);

    	$this->_setHeaders($I);
    }

    // list actions here that you want to execute once before all tests
    public function beforeAllTests(ApiTester $I)
    {
        $I->resetEmails();

        $I->callArtisan('migrate:fresh');
    }

    public function testRegistration(AuthStep $I)
    {
        $url            = '/auth/register';
        $validUrlParams = [
            'name'      => $this->faker->name,
            'email'     => $this->faker->email,
            'password'  => 'password',
        ];

        $I->_testResponseHasMessage($url, $validUrlParams, 'registered successfully', null, 200);

		$I->_testEmptyInputValidation($url);

		$I->_testInvalidEmailInputValidation($url, [ 'email' => $this->faker->name ]);

		$I->_testFieldAlreadyExistsValidation($url, 'email', $this->user->first()->email);
    }

    public function testVerifyEmail(AuthStep $I)
    {
        $url = '/auth/verify.email';
        $validUrlParams = $I->_grabMatchingUrlFromLastEmail('verify.email');

        $I->_testResponseHasMessage($url, [], 'invalid signature', 'GET', 403);

        $I->_testResponseHasMessage($url, $validUrlParams, 'verified successfully', 'GET', 200);
    }

    public function testLogin(AuthStep $I)
    {
        $url = '/auth/login';
        $validUrlParams = [ 'email' => $this->user->first()->email, 'password' => 'password' ];
        $fakeUrlParams  = [ 'email' => $this->faker->email, 'password' => $this->faker->password ];

        $I->_testEmptyInputValidation($url);

        $I->_testInvalidEmailInputValidation($url, [ 'email' => $this->faker->name ]);

        $I->_testResponseHasMessage($url, $fakeUrlParams, 'wrong email or password', null, 401);

        $I->_testResponseHasData($url, $validUrlParams, 'access_token', 'POST', 200);
    }

    public function testSendVerificationEmail(AuthStep $I)
	{
		$url = '/auth/send.verification.email';
        $validUrlParams = [ 'email' => $this->user->first()->email ];

		$I->_testEmptyInputValidation($url);

        $I->_testInvalidEmailInputValidation($url, [ 'email' => $this->faker->name ]);

		$I->_testFieldDoesNotExistValidation($url, 'email', $this->faker->email);

		$I->_testEmailIsSentSuccessfully($url, $validUrlParams);
	}

    public function testForgotPassword(AuthStep $I)
	{
		$url = '/auth/forgot.password';
        $validUrlParams = [ 'email' => $this->user->first()->email ];

		$I->_testEmptyInputValidation($url);

        $I->_testInvalidEmailInputValidation($url, [ 'email' => $this->faker->name ]);

		$I->_testFieldDoesNotExistValidation($url, 'email', $this->faker->email);

		$I->_testEmailIsSentSuccessfully($url, $validUrlParams);
	}

    public function testVerifyResetPassword(AuthStep $I)
	{
        $url = '/auth/reset.password';
        $validUrlParams     = $I->_grabMatchingUrlFromLastEmail('reset.password');
        $fakeTokenParams    = ['email' => $validUrlParams['email'], 'token' => $this->faker->text ];

		$I->_testEmptyInputValidation($url, 'GET');

        $I->_testInvalidEmailInputValidation($url, [ 'email' => $this->faker->name ], 'GET');

		$I->_testFieldDoesNotExistValidation($url, 'email', $this->faker->email, 'GET');

        $I->_testResponseHasMessage($url, $fakeTokenParams, 'could not find that token', 'GET', 400);

        $I->_testResponseHasMessage($url, $validUrlParams, 'validated successfully', 'GET', 200);
	}

    public function testResetPassword(AuthStep $I)
	{
		$url = '/auth/reset.password';
        $validUrlParams = $I->_grabMatchingUrlFromLastEmail('reset.password');
        $validUrlParams['password'] = 'password';

		$I->_testEmptyInputValidation($url);

        $I->_testInvalidEmailInputValidation($url, [ 'email' => $this->faker->name ]);

		$I->_testFieldDoesNotExistValidation($url, 'email', $this->faker->email);

        $I->_testResponseHasData($url, $validUrlParams, 'access_token', 'POST', 200);

        $I->_testResponseHasMessage($url, $validUrlParams, 'could not find that token', 'POST', 400);
	}


    public function _verifyEnvironment(ApiTester $I)
    {
        if (! App::environment('testing') ) {
            die("\n not a testing environment. exiting \n");
        }
    }

    // set headers
    public function _setHeaders(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
    }
}
