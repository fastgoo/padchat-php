<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午2:06
 */

include './vendor/autoload.php';

$callFunction = function () {
    $webSocket = new AsyncClient\WebSocket('118.89.159.190', 7777);
    $cmdId = null;
    $webSocket->setSendDataJsonEncode(false);
    $sendCmd = function ($cmd, $data = []) use ($webSocket, &$cmdId) {
        $data = ['type' => 'user', 'cmd' => $cmd, 'cmdId' => uniqid(), 'data' => $data];
        $cmdId = $data['cmdId'];
        $ret = json_encode($data, JSON_UNESCAPED_UNICODE);
        echo "发送数据：$ret \n";
        $webSocket->send($ret);
    };

    $webSocket->onMessage(function ($server, $frame) use (&$cmdId, $sendCmd) {
        $data = json_decode($frame->data);
        echo "响应数据：" . json_encode($data, JSON_UNESCAPED_UNICODE) . " \n";
        if (!empty($data->type) && !empty($data->event) && $data->type == 'userEvent' && $data->event == 'qrcode') {
            file_put_contents('./login-' . date('His') . '.png', base64_decode($data->data->qr_code));
        }
        if (!empty($data->cmdId) && $data->cmdId == $cmdId) {
            $sendCmd('login', ['loginType' => 'qrcode']);
            $cmdId = null;
        }
    });

    $webSocket->onConnect(function () use ($sendCmd) {
        $sendCmd('init');
    });
    $webSocket->connect();
};


foreach (range(1, 2) as $val) {
    $process = new \AsyncClient\SwooleProcess();
    $process->init(function ($work) use ($val, $callFunction) {
        $callFunction();
        //$work->exit();
        /*for ($i = 0; $i <= 10; $i++) {
            echo "msg: $val - $i \n";
            sleep(1);
        }*/
    });
    $process->setProcessName('wx-'.$val);
    $process->setDaemon(true);
    $pid = $process->run();
}