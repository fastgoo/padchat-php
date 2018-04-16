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
        $msg = [
            'title'=>'测试标题',
            'des'=>"测试描述",
            'url'=>'https://www.baidu.com',
            'thumburl'=>'http://wx.qlogo.cn/mmhead/ver_1/KI3hyxHcWsoicWUzJWUrwVZS1iczNeYNNR0EQ9Hq2KPAgHjF8JP3kicC2wPMrHP5CSNV0s9nTh2vObG49aFvdc5wozZokXC9psVibArhKobPgCU/132',
        ];
        $data = $client->sendMsg("wxid_t7p01dw592qt12", "wxid_k9jdv2j4n8cf12", "123");
        break;
}
var_dump($data);
