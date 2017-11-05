<?php
/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.zcloop.com
+-----------------------------------------------------------------------------------------------------------------------
| Api 返回
|
*/
namespace App\Traits\Api;

trait ApiResponse
{
    /**
     * @var int 返回的 http 状态码
     */
    private $httpCode = 200;

    /**
     * @var array 返回的 http 头部信息
     */
    private $httpHeaders = [];

    /**
     * @var int 返回的 code 状态码
     * */
    private $code = 200;

    /**
     * @var string 返回的 msg 提示
     */
    private $statusMsg = 'success';

    /**
     * @var array 返回的 msg 快捷数组
     * */
    public static $statusMsgTexts = [
        '缺少关键参数',
        '暂无数据',
        'create success',
        'create error',
        'update success',
        'update error',
        'delete success',
        'delete error',
        'restore success',
        'restore error',
    ];

    /**
     * 获取定义的错误信息
     *
     * @param int $type
     * @return string
     */
    public function getMessage($type = 0) : string
    {
        return static::$statusMsgTexts[$type] ?? 'error';
    }

    /**
     * 设置 code 状态码
     *
     * @param int $code
     * @return $this
     */
    public function setCode(int $code) : self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 设置 http 状态码
     *
     * @param int $code
     * @return $this
     */
    public function setHttpCode(int $code) : self
    {
        $this->httpCode = $code;
        return $this;
    }

    /**
     * 设置 http 头部
     *
     * @param array $header
     * @return $this
     */
    public function setHttpHeaders(array $header = []) : self
    {
        $this->httpCode = $header;
        return $this;
    }

    /**
     * 设置提示信息
     *
     * @param string|int $msg
     * @return $this
     */
    protected function setMessage($msg = null) : self
    {
        if (!is_null($msg) && is_int($msg)) {
            $msg = static::$statusMsgTexts[$msg] ?? 'error';
        }
        $this->statusMsg = $msg;

        return $this;
    }

    /**
     * 返回 json
     *
     * @param string|array|object|int
     * @param int|null $count
     * @param array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond(&$data, int $count = null, array $headers = [])
    {
        $response = [
            'code'  => $this->code,
            'data'  => $data,
            'msg'   => $this->statusMsg,
            'time'  => time()
        ];
        if (!is_null($count)) {
            $response['count'] = $count;
        }

        return response()->json($response, $this->httpCode, $headers);
    }

    /**
     * 失败
     *
     * @param string $msg
     * @param string|array|object|int $data
     * @return mixed
     */
    public function failed($msg = 'error', $data = null)
    {
        return $this->setMessage($msg)->respond($data);
    }

    /**
     * 成功
     *
     * @param string|array|object|int $data
     * @param string $msg
     * @return mixed
     */
    public function succeed($data = null, $msg = 'success')
    {
        return $this->setMessage($msg)->respond($data);
    }

    /**
     * 成功并带有总数数据
     *
     * @param string|array|object|int $data
     * @param string $msg
     * @param int|array $count
     * @return mixed
     */
    public function succeedPage($data, $count, $msg = 'success')
    {
        return $this->setMessage($msg)->respond($data, $count);
    }

    /**
     * 404
     *
     * @param string $msg
     * @return mixed
     */
    public function notFond($msg = 'Not Fond')
    {
        return $this->setCode(404)->failed($msg);
    }
}
