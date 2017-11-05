<?php

namespace App\Http\Controllers\Home\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpFoundation\Response as Foundationresponse;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    protected $statusCode = Foundationresponse::HTTP_OK;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/member';

    /**
     * @var array 获取所有提交过来的参数
     */
    protected $request = [];

    /**
     * LoginController constructor.
     *
     * @param Request $request
     * @return mixed
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest')->except('logout');
        $this->request = $request->all();
    }

    /**
     * 改写 AuthenticatesUsers trait 的 username 方法，已适应多字段登录
     *
     * @return string
     */
    public function username()
    {
        if (empty($this->request)) {
            return 'name';
        }

        $loginName = ['name', 'phone', 'email'];
        foreach ($this->request as $k => $v) {
            if (!empty($v) && in_array($k, $loginName)) {
                return $k;
            }
        }

        return 'name';
    }
}
