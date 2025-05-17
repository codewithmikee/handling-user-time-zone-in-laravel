<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseControllers\BaseApiController;
use Illuminate\Http\Request;

class ApiControllerTest extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->respondSuccess(null, 'Hello, Laravel API!');
    }
}
