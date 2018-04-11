<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 上午9:45
 */

namespace AsyncClient;

use Swoole\Http\Client as HttpClient;

class WebSocket
{
    private $client;
    private $onConnectCall;
    private $isJsonEncode = true;

    public function __construct(string $ip, int $port, int $timeout = 3)
    {
        $this->client = new HttpClient($ip, $port);
        $this->client->set(['timeout' => $timeout]);
    }

    /**
     * 消息回调
     * @param callable $call
     */
    public function onMessage(callable $call)
    {
        $this->client->on('message', function (HttpClient $server, $frame) use ($call) {
            $call($server, $frame);
        });
    }

    /**
     * 连接成功回调
     * @param callable $call
     */
    public function onConnect(callable $call)
    {
        $this->onConnectCall = $call;
    }

    /**
     * 发送数据
     * @param $data
     */
    public function send($data)
    {
        $this->client->push($this->isJsonEncode ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data);
    }

    /**
     * 设置发送数据默认格式化json
     * @param bool $isJson
     */
    public function setSendDataJsonEncode(bool $isJson)
    {
        $this->isJsonEncode = $isJson;
    }

    /**
     * 开始服务
     */
    public function connect()
    {
        $connectCall = $this->onConnectCall;
        $this->client->upgrade('/', function (HttpClient $server) use ($connectCall) {
            $connectCall($server);
        });
    }
}