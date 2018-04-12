<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午5:11
 */

namespace Padchat\Core;

use Monolog\Logger AS MonoLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    private $request;
    private $response;

    public function __construct($pid = 0)
    {
        $this->request = new MonoLogger("pid-$pid");
        $this->request->pushHandler(new StreamHandler("runtime/log/request-pid-$pid.log", MonoLogger::DEBUG));

        $this->response = new MonoLogger("pid-$pid");
        $this->response->pushHandler(new StreamHandler("runtime/log/response-pid-$pid.log", MonoLogger::DEBUG));
    }

    /**
     * 请求数据debug
     * @param $data
     */
    public function requestDebug($data)
    {
        (is_array($data) || is_object($data)) && $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->request->addDebug("请求数据：$data");
    }

    /**
     * 响应数据debug
     * @param $data
     */
    public function responseDebug($data)
    {
        (is_array($data) || is_object($data)) && $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->response->addDebug("响应数据：$data");
    }

}