<?php

namespace App\Http\Controllers\WeChat;

use EasyWeChat\OfficialAccount\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotifyController extends Controller
{
    public function send(Application $application, Request $request)
    {
        $result = $application->template_message->send(
            [
                'touser' => 'oLW_ivyL3Oh9r_maNd5whvY-wzHs',
                'template_id' => 'v-qZHxrTr-ytYofuSdQrEWky5DXhIByc8BiQIrphXnI',
                'url' => 'https://www.zcloop.com',
                'data' => [
                    'exception_router'  => '111',
                    'exception_ip'      => '22223',
                    'exception_region'  => '佛山',
                    'exception_type'    => 'http',
                    'exception_file'    => 'ss',
                    'exception_info'    => 'www',
                    'exception_line'    => '12',
                    'exception_time'    => date('Y-m-d H:i:s')
                ]
            ]
        );

        dump($result);
    }
}
