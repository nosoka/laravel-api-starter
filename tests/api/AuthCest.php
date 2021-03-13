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
    }

    public function testLogin(ApiTester $I)
    {
        // set input
        $validInput 		= [ 'email' => 'admin@admin.com', 'password' => 'admin' ];
        $fakeInput 			= [ 'email' => $this->faker->email, 'password' => $this->faker->password ];
        $invalidEmailInput	= [ 'email' => $this->faker->text ];
        $invalidEmptyInput  = [];
        $validParamsCount  	= sizeof($validInput);

        // set headers
        $I->wantTo('test /auth/login');
		$I->haveHttpHeader('accept', 'application/json');
		$I->haveHttpHeader('content-type', 'application/json');


        // test valid data
        $I->sendPOST('/auth/login', $validInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
		$I->seeResponseMatchesJsonType([ 'access_token' => 'string' ], '$.data');


        // test wrong data - random/fake data
        $I->sendPOST('/auth/login', $fakeInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(401);
        $I->seeResponseMatchesJsonType([ 'string:regex(/wrong email or password/i)'], '$.message');


        // test wrong data - empty data
        $I->sendPOST('/auth/login', $invalidEmptyInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->assertEquals($validParamsCount, sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType([ '0' => 'string:regex(/field is required/i)'], '$.errors[*]');


        // test wrong data - invalid email
        $I->sendPOST('/auth/login', $invalidEmailInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->seeResponseMatchesJsonType([ '0' => 'string:regex(/must be a valid email address/i)'], '$.errors.email[0]');
    }

    public function testRegistration(ApiTester $I)
    {
        // set input
        $invalidEmptyInput  = [];
        $invalidEmailInput 	= [ 'email' => $this->faker->name, ];
        $existingEmailInput = [ 'email' => $this->user->first()->email ];
        $validInput 		= [
        	'email' 	=> $this->faker->email,
        	'password' 	=> $this->faker->password,
        	'name' 		=> $this->faker->name
        ];
        $validParamsCount  	= sizeof($validInput);

        // set headers
        $I->wantTo('test /auth/register');
		$I->haveHttpHeader('accept', 'application/json');
		$I->haveHttpHeader('content-type', 'application/json');


        // test valid data
        $I->sendPOST('/auth/register', $validInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->assertEquals('success', $I->grabDataFromResponseByJsonPath('$.status')[0]);


        // test wrong data - empty data
        $I->sendPOST('/auth/register', $invalidEmptyInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->assertEquals($validParamsCount, sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/field is required/)'], '$.errors[*]');


        // test wrong data - email format
        $I->sendPOST('/auth/register', $invalidEmailInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->assertEquals($validParamsCount, sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/must be a valid email/)'], '$.errors.email');


        // test wrong data - duplicate data
        $I->sendPOST('/auth/register', $existingEmailInput);
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(422);
        $I->assertEquals($validParamsCount, sizeof($I->grabDataFromResponseByJsonPath('$.errors')[0]));
        $I->seeResponseMatchesJsonType(['0' => 'string:regex(/has already been taken/)'], '$.errors.email');
    }
}
