<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseControllers\ProtectedApiController;
use App\Models\User;

class NewController extends ProtectedApiController
{
    public function index(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, User $dummyModel)
    {
        //
    }

    public function destroy(Request $request, User $dummyModel)
    {
        //
    }
}
