<?php

namespace App\Http\Controllers\WeChat;

use Illuminate\Http\Request;
use EasyWeChat\OfficialAccount\Application;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index(Application $application)
    {
        return $application->server->serve();
    }
}
