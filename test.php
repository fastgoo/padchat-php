<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/14
 * Time: 下午4:43
 */
/** redis注册 */
include './vendor/autoload.php';

define('BASE_PATH', __DIR__);

use Padchat\Client;


$client = new Client([
    'host' => '127.0.0.1',
    'port' => 6379,
    'auth' => '',
]);

switch ($argv[1]) {
    case 'login_qrcode':
        $data = $client->getLoginQrcode();
        break;
    case 'login_status':
        $data = $client->getLoginStatus();
        break;
    case 'login_success_info':
        $data = $client->getLoginSuccessInfo();
        break;
    case 'login_wx_info':
        $data = $client->getWxInfo("wxid_t7p01dw592qt12");
        break;
    case 'friend_list':
        $data = $client->getFriendList("wxid_t7p01dw592qt12", 1, 1, 15);
        break;
    case 'gh_list':
        $data = $client->getFriendList("wxid_t7p01dw592qt12", 1, 1, 15);
        break;
    case 'group_list':
        $data = $client->getFriendList("wxid_t7p01dw592qt12", 1, 1, 15);
        break;
    case 'send_msg':
        $msg2 = [
            'title' => '测试标题',
            'des' => "测试描述",
            'url' => 'https://www.baidu.com',
            'thumburl' => 'http://wx.qlogo.cn/mmhead/ver_1/KI3hyxHcWsoicWUzJWUrwVZS1iczNeYNNR0EQ9Hq2KPAgHjF8JP3kicC2wPMrHP5CSNV0s9nTh2vObG49aFvdc5wozZokXC9psVibArhKobPgCU/132',
        ];
        $msg = [
            'image' => $client->getLoginQrcode(),
        ];
        $msg = [
            'userId' => 'wxid_t7p01dw592qt12',
            'content' => '这个大牛很屌',
        ];
        $msg = [
            'voice'=>'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',
        ];
        $data = $client->sendMsg("wxid_t7p01dw592qt12", "wxid_k9jdv2j4n8cf12", $msg2);
        break;
}
var_dump($data);
