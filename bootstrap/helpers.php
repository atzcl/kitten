<?php
/*
+-----------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.zcloop.com
+-----------------------------------------------------------------------------------
| 自定义辅助函数
|
*/

if (! function_exists('weChatOptions')) {
    /**
     * 获取微信配置缓存
     *
     * @param string $oauth_callback  OAuth授权完成后的回调页地址
     * @param string $notify_url 微信支付订单回调地址
     * @return array
     * */
    function weChatOptions(string $oauth_callback, string $notify_url) : array
    {
        $weChatCache = Cache::get('wechat_config');

        // 返回配置
        return [
            /**
             * Debug 模式，bool 值：true/false
             *
             * 当值为 false 时，所有的日志都不会记录
             */
            'debug'  => true,
            /**
             * 账号基本信息，请从微信公众平台/开放平台获取
             */
            'app_id'  => $weChatCache['app_id'],               // AppID
            'secret'  => $weChatCache['app_secret'],          // AppSecret
            'token'   => $weChatCache['token'],               // Token
            'aes_key' => $weChatCache['encoding_aes_key'],    // EncodingAESKey，安全模式下请一定要填写！！！

            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：
             *         debug/info/notice/warning/error/critical/alert/emergency
             * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
             * file：日志文件位置(绝对路径!!!)，要求可写权限
             */
            'log' => [
                'level'      => 'debug',
                'permission' => 0777,
                'file'       => LOG_PATH . 'wechat_log/wechat_'.date('Ymd').'.log',
            ],
            /**
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址
             */
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => $oauth_callback,
            ],
            /**
             * 微信支付
             */
            'payment' => [
                'merchant_id'        => $weChatCache['merchant_id'],
                'key'                => $weChatCache['partnerkey'],
                'cert_path'          => './cert/apiclient_cert.pem', // 证书: 绝对路径，这里去掉一个斜杠
                'key_path'           => './cert/apiclient_key.pem',  // 证书: 绝对路径，这里去掉一个斜杠
                'notify_url'         => $notify_url,       // 下单回调
            ],
            /**
             * Guzzle 全局设置
             *
             * 更多请参考： http://docs.guzzlephp.org/en/latest/request-options.html
             */
            'guzzle' => [
                'timeout' => 3.0, // 超时时间（秒）
                //'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
            ],
        ];
    }
}

if (! function_exists('smsOptions')) {
    /**
     * SMS 配置
     *
     * @param string $signName 短信签名
     * @return array
     * */
    function smsOptions(string $signName) : array
    {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun'
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => LOG_PATH . 'sms_log/sms_'.date('Ymd').'.log',
                ],
                'aliyun' => [
                    'access_key_id' => 'LTAI8sD0ghTIt0dl',
                    'access_key_secret' => 'QQy9f2pE94g2wtSmXqvEEy7Cm0KZgg',
                    'sign_name' => $signName,
                ]
            ],
        ];


        return $config;
    }
}

if (! function_exists('returnToJson')) {
    /**
     * 返回api解释
     *
     * @param int $code 状态码
     * @param array|null $data 返回数据
     * @param int $count 总条数
     * @param string $msg 提示
     * @return \Illuminate\Http\JsonResponse
     * */
    function returnToJson($data = null, string $msg = 'success', int $code = 200, int $count = null)
    {
        $response = [
            'code'  => $code,
            'data'  => $data,
            'msg'   => $msg,
            'time'  => time()
        ];
        if (!is_null($count)) {
            $response['count'] = $count;
        }

        return response()->json($response);
    }
}

if (! function_exists('throwApiError')) {
    /**
     * 快捷抛出 api 异常
     *
     * @param int $code
     * @param string $message
     * @throws \App\Exceptions\ApiException
     */
    function throwApiError(int $code, string $message)
    {
        throw new \App\Exceptions\ApiException($code, $message);
    }
}
