<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午4:42
 */

namespace Padchat;

use Padchat\Core\Ioc as PadchatDi;
use Padchat\Core\TaskIoc;
use Padchat\Core\Receive;

class Callback
{
    private $webSocket;

    public function __construct()
    {

    }

    public function handle()
    {
        //初始化设备
        if (PadchatDi::getDefault()->get('receive')->isInit()) {
            PadchatDi::getDefault()->get('log')->responseDebug("微信客户端实例化成功：");
            PadchatDi::getDefault()->get('api')->login();
        }
        //获取登录二维码成功
        if ($ret = PadchatDi::getDefault()->get('receive')->isGetQrcodeSuccess()) {
            PadchatDi::getDefault()->get('log')->responseDebug("获取二维码成功：" . json_encode($ret, JSON_UNESCAPED_UNICODE));
            file_put_contents(BASE_PATH . '/runtime/qrcode/login-' . date('His') . '.png', base64_decode($ret->qr_code));
        }
        //等待扫码
        if ($ret = PadchatDi::getDefault()->get('receive')->isWaitScan()) {
            PadchatDi::getDefault()->get('log')->responseDebug("等待扫码");
        }
        //等待确认
        if ($ret = PadchatDi::getDefault()->get('receive')->isWaitConfirm()) {
            PadchatDi::getDefault()->get('log')->responseDebug("已扫码等待确认：昵称({$ret->nick_name}) 头像({$ret->head_url})");
        }
        //登录成功
        if ($ret = PadchatDi::getDefault()->get('receive')->isLoginSuccess()) {
            TaskIoc::getDefault()->set('wxid', $ret->user_name);
            PadchatDi::getDefault()->get('log')->responseDebug("登录成功：wxid({$ret->user_name})");
        }
        //获取好友列表回调
        if ($ret = PadchatDi::getDefault()->get('receive')->isGetFriendsList()) {
            PadchatDi::getDefault()->get('log')->responseDebug("获取好友列表 " . json_encode($ret, JSON_UNESCAPED_UNICODE));
        }
        //消息事件回调
        if ($ret = PadchatDi::getDefault()->get('receive')->isMessageCallback()) {
            $this->message($ret);
            if (PadchatDi::getDefault()->get('receive')->isAtMe()) {
                PadchatDi::getDefault()->get('api')->sendMsg($ret->from_user, "收到@消息");
                PadchatDi::getDefault()->get('log')->responseDebug("有人@我 ");
            }
            PadchatDi::getDefault()->get('log')->responseDebug("收到消息通知 " . json_encode($ret, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * // 1  文字消息
     * // 2  好友信息推送，包含好友，群，公众号信息
     * // 3  收到图片消息
     * // 34  语音消息
     * // 35  用户头像buf
     * // 37  收到好友请求消息
     * // 42  名片消息
     * // 43  视频消息
     * // 47  表情消息
     * // 48  定位消息
     * // 49  APP消息(文件 或者 链接 H5)
     * // 50  语音通话
     * // 51  状态通知（如打开与好友/群的聊天界面）
     * // 52  语音通话通知
     * // 53  语音通话邀请
     * // 62  小视频
     * // 2000  转账消息
     * // 2001  收到红包消息
     * // 3000  群邀请
     * // 9999  系统通知
     * // 10000  微信通知信息. 微信群信息变更通知，多为群名修改，进群，离群信息，不包含群内聊天信息
     * // 10002  撤回消息
     * 收到消息处理机制
     * @param $data
     */
    private function message($data)
    {
        if ($data->from_user != 'wxid_k9jdv2j4n8cf12') {
            return;
        }
        switch ($data->sub_type) {
            case 1:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到文字消息");
                break;
            case 3:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到图片消息");
                break;
            case 34:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收语音消息");
                break;
            case 35:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到头像BUFF消息");
                break;
            case 37:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到好友请求");
                break;
            case 42:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到名片消息");
                break;
            case 43:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到视频消息");
                break;
            case 47:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到表情消息");
                break;
            case 48:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到定位消息");
                break;
            case 49:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到APP消息");
                break;
            case 50:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到语音通话消息");
                break;
            case 51:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到状态通知消息");
                break;
            case 52:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到通话通知消息");
                break;
            case 53:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到通话邀请消息");
                break;
            case 62:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到小视频消息");
                break;
            case 2000:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到转账消息");
                break;
            case 2001:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到红包消息");
                break;
            case 3000:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到群邀请消息");
                break;
            case 9999:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到系统通知消息");
                break;
            case 10000:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到微信通知信息消息");
                break;
            case 10002:
                PadchatDi::getDefault()->get('api')->sendMsg($data->from_user, "收到撤回消息");
                break;
        }
    }
}