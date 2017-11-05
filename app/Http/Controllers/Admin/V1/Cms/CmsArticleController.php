<?php
/*
+-----------------------------------------------------------------------------------
| Author: ZhiChengLiang <atzcl0310@gmail.com>  Blog：https://www.zcloop.com
+-----------------------------------------------------------------------------------
*/

namespace App\Http\Controllers\Admin\V1\Cms;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class CmsArticleController extends Controller
{
    public function index()
    {
        $collect = collect([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);
        dd($collect->collapse()->all());
    }
}
