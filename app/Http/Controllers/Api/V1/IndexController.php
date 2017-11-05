<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        throwApiError(1090, 'test');
    }
}
