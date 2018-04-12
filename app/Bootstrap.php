<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/12
 * Time: 下午4:02
 */

namespace Padchat;

use Padchat\Core\Ioc as PadchatDi;
use AsyncClient\WebSocket;
use Padchat\Core\Logger;
use Padchat\Core\Receive;
use AsyncClient\SwooleProcess;

class Bootstrap
{
    private $callFunc;
    private $config;

    public function __construct()
    {
        $this->init();
        $this->config = include_once BASE_PATH . "/app/config.php";

    }

    /**
     * 初始化服务
     */
    public function init()
    {

        $this->callFunc = function (int $pid = 0) {
            /** 注册配置信息 */
            PadchatDi::getDefault()->set('config', function () {
                return json_decode(json_encode($this->config));
            });
            /** 注册websocket服务类 */
            PadchatDi::getDefault()->set('websocket', function () {
                $config = PadchatDi::getDefault()->get('config');
                return new WebSocket($config->server->host, $config->server->port);
            });
            /** 注册api接口类 */
            PadchatDi::getDefault()->set('api', function () use ($pid) {
                return new Api();
            });
            /** 注册websocket数据回调类 */
            PadchatDi::getDefault()->set('callback', function () use ($pid) {
                return new Callback();
            });
            /** 注入消息处理类 */
            PadchatDi::getDefault()->set('receive', function () use ($pid) {
                return new Receive();
            });
            /** 注入日志类 */
            PadchatDi::getDefault()->set('log', function () use ($pid) {
                return new Logger($pid);
            });
            /** websocket默认使用json数据发送 */
            PadchatDi::getDefault()->get('websocket')->setSendDataJsonEncode(false);
            /** 设置消息回调 */
            PadchatDi::getDefault()->get('websocket')->onMessage(function ($server, $frame) {
                $config = PadchatDi::getDefault()->get('config');
                if ($config->debug->cmd) {
                    echo "响应：$frame->data";
                }
                if ($config->debug->response) {
                    PadchatDi::getDefault()->get('log')->responseDebug($frame->data);
                }
                PadchatDi::getDefault()->get('receive')->setParams(json_decode($frame->data));
                PadchatDi::getDefault()->get('callback')->handle();
            });
            /** 设置连接回调 */
            PadchatDi::getDefault()->get('websocket')->onConnect(function () {
                PadchatDi::getDefault()->get('api')->send('init');
            });
            /** 连接服务 */
            PadchatDi::getDefault()->get('websocket')->connect();
        };
    }

    /**
     * 运行服务
     * @return array
     */
    public function run()
    {
        $call = $this->callFunc;
        $pid = [];
        /** 如果没开进程跑付，则阻塞运行。开进程可运行多个服务 */
        if (!$this->config['process']['status']) {
            $call();
            $pid[] = 0;
        } else {
            for ($i = 1; $i <= $this->config['process']['count']; $i++) {
                $process = new SwooleProcess();
                $process->init(function ($work) use ($call) {
                    $call($work->pid);
                    //$work->exit();
                });
                $process->setProcessName('padchat-php-index-' . $i);
                $pid[] = $process->run();
            }

        }
        return $pid;
    }

    public function stop()
    {

    }

    public function reload()
    {

    }
}