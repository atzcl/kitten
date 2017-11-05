<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Api\JWT;

class IndexController extends Controller
{
    use JWT;
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    public function index()
    {
        return view('welcome');
    }

    public function member()
    {
        return view('home');
    }
}
