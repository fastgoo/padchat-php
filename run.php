<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午4:40
 */
include './vendor/autoload.php';

use Padchat\Core\Ioc as PadchatDi;
use AsyncClient\WebSocket;
use Padchat\Core\Logger;
use Padchat\Api;
use Padchat\Callback;


$callFunction = function (int $pid) {
    /** 注册websocket服务类 */
    PadchatDi::getDefault()->set('websocket', function () use ($pid) {
        return new WebSocket('118.89.159.190', 7777);
    });
    /** 注册api接口类 */
    PadchatDi::getDefault()->set('api', function () use ($pid) {
        return new Api();
    });
    /** 注册websocket数据回调类 */
    PadchatDi::getDefault()->set('callback', function () use ($pid) {
        return new Callback();
    });
    /** 注入日志类 */
    PadchatDi::getDefault()->set('log', function () use ($pid) {
        return new Logger($pid);
    });
    PadchatDi::getDefault()->get('websocket')->setSendDataJsonEncode(false);
    PadchatDi::getDefault()->get('websocket')->onMessage(function ($server, $frame) {
        echo "响应：$frame->data";
        PadchatDi::getDefault()->get('callback')->handle($frame->data);
    });
    PadchatDi::getDefault()->get('websocket')->onConnect(function (){
        PadchatDi::getDefault()->get('api')->send('init');
    });
    PadchatDi::getDefault()->get('websocket')->connect();
};
$callFunction(0);
return
$process = new \AsyncClient\SwooleProcess();
$process->init(function ($work) use ($val, $callFunction) {
    $callFunction($work->pid);
    //$work->exit();
    /*for ($i = 0; $i <= 10; $i++) {
        echo "msg: $val - $i \n";
        sleep(1);
    }*/
});
$process->setProcessName('php wx-1');
$process->setDaemon(true);
$pid = $process->run();
echo 1;
return;
