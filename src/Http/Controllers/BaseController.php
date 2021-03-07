<?php

namespace Api\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use Helpers;

    public function sendMessageResponse(string $messageResponse = null)
    {
        return response()->json([
            'status_code'   => 200,
            'status'        => 'success',
            'message'       => $messageResponse
        ]);
    }

    public function sendArrayresponse(array $arrayResponse = [])
    {
        return response()->json([
            'status_code'   => 200,
            'status'        => 'success',
            'data'       	=> $arrayResponse
        ]);
    }
}
