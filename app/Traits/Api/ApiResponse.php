<?php

declare( strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| Api 返回
|
*/

namespace App\Traits\Api;

// Response represents an HTTP response
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

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
    private $statusCode = 200;

    /**
     * @var mixed 返回的 data 数据
     * */
    private $statusData = null;

    /**
     * @var string 返回的 msg 提示
     */
    private $statusMessage = 'success';

    /**
     * @var int|null 返回的分页查询的总页数
     */
    private $statusTotal = null;

    /**
     * @var array 返回的 msg 快捷数组
     * */
    public static $statusMessageTexts = [
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
    public function getStatusMessage($type = 0): string
    {
        return $this->statusMessage ?? static::$statusMessageTexts[$type];
    }

    /**
     * 设置 code 状态码
     *
     * @param int $code
     * @return $this
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;

        return $this;
    }

    /**
     * 设置 http 状态码
     *
     * @param int $code
     * @return $this
     */
    public function setHttpCode(int $code): self
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
    public function setHttpHeaders(array $header = []): self
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
    public function setStatusMessage($msg = null): self
    {
        if (!is_null($msg) && is_int($msg)) {
            $msg = static::$statusMessageTexts[$msg] ?? 'error';
        }

        $this->statusMessage = $msg;

        return $this;
    }

    /**
     * 设置返回的 data 数据
     *
     * @param mixed $data
     * @return $this
     */
    public function setStatusData($data = null): self
    {
        $this->statusData = $data;

        return $this;
    }

    /**
     * 设置分页查询的总页数
     *
     * @param int $value
     * @return $this
     */
    public function setStatusTotal(int $value): self
    {
        $this->statusTotal = $value;

        return $this;
    }

    /**
     * 返回 json
     *
     * @param array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond(array $headers = [])
    {
        $response = [
            'code'  => $this->statusCode,
            'data'  => $this->statusData,
            'msg'   => $this->statusMessage,
            'time'  => time()
        ];

        if (!is_null($this->statusTotal)) {
            $response['total'] = $this->statusTotal;
        }

        return response()->json($response, $this->httpCode, $headers)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ->header('content-type', 'application/json; charset=UTF-8');
    }

    /**
     * 失败
     *
     * @param string $msg
     * @return mixed
     */
    public function failed(string $msg = '')
    {
        if (!empty($msg)) {
            $this->setStatusMessage($msg);
        }

        return $this->setStatusMessage($msg)->respond();
    }

    /**
     * 成功
     *
     * @param string $msg
     * @return mixed
     */
    public function succeed(string $msg = '')
    {
        if (!empty($msg)) {
            $this->setStatusMessage($msg);
        }

        return $this->respond();
    }

    /**
     * 成功并带有总数数据
     *
     * @param string $msg
     * @param int|array $count
     * @return mixed
     */
    public function succeedPage($count, $msg = 'success')
    {
        return $this->setStatusMessage($msg)->respond($count);
    }

    /**
     * 404
     *
     * @param string $msg
     * @return mixed
     */
    public function notFond($msg = 'Not Fond')
    {
        return $this->setStatusCode(404)->failed($msg);
    }
}
