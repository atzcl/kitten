<?php

declare( strict_types = 1);

/*
+-----------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------
| 自定义全局辅助函数
|
*/


if (! function_exists('generate_uuid')) {
    /**
     * 生成唯一随机的 UUID
     *
     *
     * @return string
     * @throws Exception
     */
    function generate_uuid()
    {
        return (string) \Webpatser\Uuid\Uuid::generate(4);
    }
}

if (! function_exists('generate_rand')) {
    /**
     * 随机生成一个字符串
     *
     * @param int $length 字符串的长度
     * @param string $type 类型
     * @return string
     */
    function generate_rand(int $length = 8, string $type = 'all')
    {
        $number = '0123456789';
        $letter = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ`~!@#$%^&*()_+-=[]{};:"|,.<>/?';

        switch ($type) {
            case 'number':
                $chars = $number;
                break;
            case 'letter':
                $chars = $letter;
                break;
            default:
                $chars = $letter . $number;
        }

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $str;
    }
}
