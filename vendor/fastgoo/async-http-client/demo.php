<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/2/11
 * Time: 下午4:58
 */
include './vendor/autoload.php';

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
return;


\AsyncClient\Client::init("https://www.baidu.com")->get('/s?wd=兔兔', function (\Swoole\Http\Client $client) use (&$count) {
    file_put_contents('log.html', $client->body);
    var_dump($client->body);
})->send();
exit;

\AsyncClient\Client::init("https://timgsa.baidu.com")->download('/timg?image&quality=80&size=b9999_10000&sec=1521617943&di=913c0898b55cf2992d6d5136013e98d2&imgtype=jpg&er=1&src=http%3A%2F%2Fimg.taopic.com%2Fuploads%2Fallimg%2F120727%2F201995-120HG1030762.jpg', './logoaa.png', function (\Swoole\Http\Client $client) {
    var_dump($client);
})->send();
//exit;
$params = [
    'address' => '773729704@qq.com',
    'subject' => '标题',
    'body' => '内容',
];
\AsyncClient\Client::init("https://open.fastgoo.net")->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
    var_dump($client->body);
})->send();

$params = [
    'address' => '773729704@qq.com',
    'subject' => '标题2',
    'body' => '内容2',
];
\AsyncClient\Client::init("https://open.fastgoo.net")
    ->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
        var_dump($client->body);
    })
    ->send();

$client = new \AsyncClient\Client("https://timgsa.baidu.com");
$client->download('/timg?image&quality=80&size=b9999_10000&sec=1521617943&di=913c0898b55cf2992d6d5136013e98d2&imgtype=jpg&er=1&src=http%3A%2F%2Fimg.taopic.com%2Fuploads%2Fallimg%2F120727%2F201995-120HG1030762.jpg', './logoaa.png', function (\Swoole\Http\Client $client) {
    var_dump($client);
});
$client->send();
exit;


/**
 * 单域名单请求
 */
$client = new \AsyncClient\Client("https://open.fastgoo.net");
$params = [
    'address' => '邮箱',
    'subject' => '标题',
    'body' => '内容',
];
$client->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
    var_dump($client->body);
});
$client->send();


/**
 * 单域名多请求
 */
$client = new \AsyncClient\Client("https://open.fastgoo.net");
$params = [
    'address' => '邮箱',
    'subject' => '标题',
    'body' => '内容',
];
foreach (range(1, 50) as $v) {
    $client->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
        var_dump($client->body);
    });
}
$client->send();

/**
 * 多域名多请求
 */
//第一个
$client = new \AsyncClient\Client("https://open.fastgoo.net");
$params = [
    'address' => '邮箱',
    'subject' => '标题',
    'body' => '内容',
];
foreach (range(1, 50) as $v) {
    $client->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
        var_dump($client->body);
    });
}
$client->send();
//第二个
$client2 = new \AsyncClient\Client("https://open.fastgoo.net");
$params2 = [
    'address' => '邮箱',
    'subject' => '标题',
    'body' => '内容',
];
$client2->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
    var_dump($client->body);
});
$client2->send();

/**
 *
 */


$client = new \AsyncClient\Client("https://open.fastgoo.net");

/*foreach (range(1,2) as $v){
    $client->post('/base.api/email/send', [
        'address' => '773729704@qq.com',
        'subject' => '666--'.$v,
        'body' => '666',
    ], function (\Swoole\Http\Client $client) {
        echo $client->body;
    });
}
$client->send();*/

//$client->setDnsIp('39.108.134.88');
$client->setFiles(['files' => __DIR__ . '/fastgoo-logo.png'])->post(
    '/base.api/file/upload',
    [],
    function (\Swoole\Http\Client $client) {
        var_dump($client->body);
    }
)->send();

/*$client->post('/base.api/file/upload', [
    'address' => '773729704@qq.com',
    'subject' => '666',
    'body' => '666',
], function (\Swoole\Http\Client $client) {
    var_dump($client->body);
});*/
