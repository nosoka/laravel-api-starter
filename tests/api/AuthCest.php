<?php

use Api\Models\User;
use Faker\Factory;

class AuthCest
{
    protected $faker;
    protected $user;

    public function _before(ApiTester $I, User $user)
    {
    	$this->faker = Factory::create();
    	$this->user  = $user;

    	$this->_setHeaders($I);
    }

    // set headers
	public function _setHeaders(ApiTester $I)
    {
		$I->haveHttpHeader('accept', 'application/json');
		$I->haveHttpHeader('content-type', 'application/json');
    }

    public function testLogin(ApiTester $I)
    {
        // set input
		$url 				= '/auth/login';
        $validInput 		= [ 'email' => 'admin@admin.com', 'password' => 'admin' ];
        $invalidFakeInput	= [ 'email' => $this->faker->email, 'password' => $this->faker->password ];

		$this->_testEmptyInputValidation($I, $url);
		$this->_testInvalidEmailFormatValidation($I, $url);

        // test invalid input - random/fake input
        $I->sendPOST($url, $invalidFakeInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(401);
        $I->seeResponseMatchesJsonType([ 'string:regex(/wrong email or password/i)' ], '$.message');

        // test valid input
        $I->sendPOST($url, $validInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
		$I->seeResponseMatchesJsonType([ 'access_token' => 'string' ], '$.data');
    }

    public function testRegistration(ApiTester $I)
    {
		$url 		= '/auth/register';
        $validInput = [
 			'email' => $this->faker->email, 'password' => $this->faker->password, 'name' => $this->faker->name
        ];

		$this->_testEmptyInputValidation($I, $url);
		$this->_testInvalidEmailFormatValidation($I, $url);
		$this->_testFieldExistsValidation($I, $url, 'email', $this->user->first()->email);

        // test valid input
        $I->sendPOST($url, $validInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType([ 'string:regex(/registered successfully/)' ], '$.message');
    }

	// TODO:: add validations
    public function _testVerifyEmail(ApiTester $I)
	{
		$url = '/auth/verify.email';
	}

    public function testSendVerificationEmail(ApiTester $I)
	{
		$url 		= '/auth/send.verification.email';
        $validInput = [ 'email' => $this->user->first()->email ];

		$this->_testEmptyInputValidation($I, $url);

		$this->_testInvalidEmailFormatValidation($I, $url);

		$this->_testFieldDoesNotExistValidation($I, $url, 'email', $this->faker->email);

		$this->_testEmailIsSentSuccessfully($I, $url, $validInput);
	}

    public function testForgotPassword(ApiTester $I)
	{
		$url 		= '/auth/forgot.password';
        $validInput = [ 'email' => $this->user->first()->email ];

		$this->_testEmptyInputValidation($I, $url);

		$this->_testInvalidEmailFormatValidation($I, $url);

		$this->_testFieldDoesNotExistValidation($I, $url, 'email', $this->faker->email);

		$this->_testEmailIsSentSuccessfully($I, $url, $validInput);
	}

    public function testVerifyResetPassword(ApiTester $I)
	{
		$url = '/auth/reset.password';

		$this->_testEmptyInputValidation($I, $url, 'GET');

		$this->_testInvalidEmailFormatValidation($I, $url, 'GET');

		$this->_testFieldDoesNotExistValidation($I, $url, 'email', $this->faker->email, 'GET');

		$this->_testFieldDoesNotExistValidation($I, $url, 'token', $this->faker->name, 'GET');
	}

    public function testResetPassword(ApiTester $I)
	{
		$url = '/auth/reset.password';

		$this->_testEmptyInputValidation($I, $url);

		$this->_testInvalidEmailFormatValidation($I, $url);

		$this->_testFieldDoesNotExistValidation($I, $url, 'email', $this->faker->email);

		$this->_testFieldDoesNotExistValidation($I, $url, 'token', $this->faker->name);

		// TODO:: test valid input
		// call /auth/forgot.password lookup the token in db and call /auth/reset.password with token
	}

    // test form validation when input is empty
	public function _testEmptyInputValidation(ApiTester $I, string $url, string $method = null)
	{
		$urlParams = [];

		(strtoupper($method) == 'GET')
			? $I->sendGET($url, $urlParams)
			: $I->sendPOST($url, $urlParams);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->assertGreaterThan(0, sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType([ 'string:regex(/field is required/)' ], '$.errors[*]');
	}

    // test form validation that input is not in valid email format
	public function _testInvalidEmailFormatValidation(ApiTester $I, string $url, string $method = null)
	{
		$urlParams = [ 'email' => $this->faker->text ];

		(strtoupper($method) == 'GET')
			? $I->sendGET($url, $urlParams)
			: $I->sendPOST($url, $urlParams);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->seeResponseMatchesJsonType([ 'string:regex(/must be a valid email/i)' ], '$.errors.email[0]');
    }

    // test form validation that random/fake input does not exist in database
	public function _testFieldDoesNotExistValidation(ApiTester $I, string $url, string $fieldName, string $fieldValue, string $method = null)
	{
        $urlParams 	= [ $fieldName => $fieldValue ];

		(strtoupper($method) == 'GET')
			? $I->sendGET($url, $urlParams)
			: $I->sendPOST($url, $urlParams);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->seeResponseMatchesJsonType([ 'string:regex(/selected(.*)is invalid/i)' ], "$.errors.{$fieldName}[0]");
    }

    // test form validation that input exists in database
	public function _testFieldExistsValidation(ApiTester $I, string $url, string $fieldName, string $fieldValue, string $method = null)
	{
        $urlParams 	= [ $fieldName => $fieldValue ];

		(strtoupper($method) == 'GET')
			? $I->sendGET($url, $urlParams)
			: $I->sendPOST($url, $urlParams);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->seeResponseMatchesJsonType([ 'string:regex(/has already been taken/i)' ], "$.errors.{$fieldName}[0]");
    }

    // test valid input - email
    // TODO:: check that email is actually sent
	public function _testEmailIsSentSuccessfully(ApiTester $I, string $url, array $urlParams, string $method = null)
	{
		(strtoupper($method) == 'GET')
			? $I->sendGET($url, $urlParams)
			: $I->sendPOST($url, $urlParams);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->seeResponseMatchesJsonType([ 'string:regex(/sent(.*)mail successfully/i)' ], '$.message');
    }
}
