<?php

namespace Step\Api;

use Faker\Factory;

class AuthStep extends \ApiTester
{
    public function _sendInput(string $url, array $urlParams, string $formMethod= null)
    {
    	$I = $this;
        if ( is_null($formMethod) || strtoupper($formMethod) == 'POST' ) {
            $I->sendPOST($url, $urlParams);
        }

        if ( strtoupper($formMethod) == 'GET' ) {
            $I->sendGET($url, $urlParams);
        }
    }

    public function _testResponseHasMatchingStringInField(string $url, array $urlParams,
        string $searchField, string $searchValue, string $formMethod = null, string $responseCode = null)
    {
    	$I = $this;
        (! is_null($responseCode) ) ? $responseCode : 200;

        $this->_sendInput($url, $urlParams, $formMethod);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs($responseCode);
        $I->seeResponseMatchesJsonType([ 'string:regex(/' .$searchValue. '/i)' ], "$.{$searchField}");
    }

    public function _testResponseHasData(string $url, array $urlParams, string $searchKey,
        string $formMethod = null, string $responseCode = null)
    {
    	$I = $this;
        (! is_null($responseCode) ) ? $responseCode : 200;

        $this->_sendInput($url, $urlParams, $formMethod);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs($responseCode);
        $I->seeResponseMatchesJsonType([ $searchKey => 'string' ], '$.data');
    }

    // test form validation when input is empty
	public function _testEmptyInputValidation(string $url, string $formMethod = null)
	{
        $urlParams   = [];
        $searchField = 'errors[*]';
        $searchValue = 'field is required';

        $this->_testResponseHasMatchingStringInField($url, $urlParams, $searchField, $searchValue, $formMethod, 422);
	}

    // test form validation that input is not in valid email format
	public function _testInvalidEmailInputValidation(string $url, $urlParams, string $formMethod = null)
	{
        $searchField = 'errors.email[0]';
        $searchValue = 'must be a valid email';

        $this->_testResponseHasMatchingStringInField($url, $urlParams, $searchField, $searchValue, $formMethod, 422);
    }

    // test form validation that random/fake input does not exist in database
	public function _testFieldDoesNotExistValidation(string $url, string $fieldName, string $fieldValue,
        string $formMethod = null)
	{
        $urlParams 	 = [ $fieldName => $fieldValue ];
        $searchField = "errors.{$fieldName}[0]";
        $searchValue = 'selected(.*)is invalid';

        $this->_testResponseHasMatchingStringInField($url, $urlParams, $searchField, $searchValue, $formMethod, 422);
    }

    // test form validation that input exists in database
	public function _testFieldAlreadyExistsValidation(string $url, string $fieldName, string $fieldValue,
        string $formMethod = null)
	{
        $urlParams 	 = [ $fieldName => $fieldValue ];
        $searchField = "errors.{$fieldName}[0]";
        $searchValue = 'has already been taken';

        $this->_testResponseHasMatchingStringInField($url, $urlParams, $searchField, $searchValue, $formMethod, 422);
    }

    public function _testResponseHasMessage(string $url, array $urlParams, string $searchValue,
        string $formMethod = null, string $responseCode = null)
    {
        $searchField = "message";
        $this->_testResponseHasMatchingStringInField(
            $url, $urlParams, $searchField, $searchValue, $formMethod, $responseCode
        );
    }

    // test valid input - email
	public function _testEmailIsSentSuccessfully(string $url, array $urlParams)
	{
		$I = $this;

        $this->_testResponseHasMessage($url, $urlParams, 'sent(.*)mail successfully', null, 200);

        $I->seeInLastEmail($urlParams['email']);
    }

    public function _grabMatchingUrlFromLastEmail(string $searchString)
    {
		$I = $this;
        $urls               = $I->grabUrlsFromLastEmail();
        $firstMatchingUrl   = collect($urls)->unique()->filter(function ($item, $key) use ($searchString) {
            return (strpos($item, $searchString) !== false) ?: false;
        })->first();

        if (! $firstMatchingUrl) {
            return false;
        }

        parse_str( parse_url( $firstMatchingUrl )['query'], $matchingUrlParams );

        return $matchingUrlParams;
    }
}
