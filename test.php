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
        $data = $client->getWxInfo("wxid_zizd4h0uzffg22");
        break;
    case 'friend_list':
        $data = $client->getFriendList("wxid_zizd4h0uzffg22", 1, 1, 15);
        break;
    case 'gh_list':
        $data = $client->getFriendList("wxid_zizd4h0uzffg22", 1, 1, 15);
        break;
    case 'group_list':
        $data = $client->getFriendList("wxid_zizd4h0uzffg22", 1, 1, 15);
        break;
    case 'send_msg':
        $data = $client->sendMsg("wxid_zizd4h0uzffg22", "wxid_k9jdv2j4n8cf12", '666');
        break;
}
var_dump($data);
