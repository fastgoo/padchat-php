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

class Callback
{
    private $webSocket;

    public function __construct()
    {
        $this->webSocket = PadchatDi::getDefault()->get('websocket');
    }

    public function handle(string $data)
    {
        //PadchatDi::getDefault()->get('log')->responseDebug($data);
        $data = json_decode($data);
        if (!empty($data->type)) {
            switch ($data->type) {
                case 'cmdRet':
                    if ($ret = TaskIoc::getDefault()->get($data->cmdId)) {
                        $this->cmdRet($ret, $data);
                    }
                    break;
                case 'userEvent':
                    $this->userEvent($data);
                    break;
                case '':
                    break;
                case '':
                    break;
                case '':
                    break;
                case '':
                    break;
                case '':
                    break;
                case '':
                    break;
            }
        }
    }

    private function userEvent($data)
    {
        switch ($data->event) {
            case 'scan':
                if (!empty($data->data->user_name)) {
                    PadchatDi::getDefault()->get('log')->responseDebug("登录成功：wxid({$data->data->user_name})");
                } elseif ($data->data->status == 0) {
                    PadchatDi::getDefault()->get('log')->responseDebug("等待扫码");
                } elseif ($data->data->status == 1) {
                    PadchatDi::getDefault()->get('log')->responseDebug("已扫码等待确认：昵称({$data->data->nick_name}) 头像({$data->data->head_url})");
                }
                break;
            case 'qrcode':
                file_put_contents('./runtime/qrcode/login-' . date('His') . '.png', base64_decode($data->data->qr_code));
                break;
            case 'push':
                if (isset($data->data->list[1]->big_head)) {
                    PadchatDi::getDefault()->get('log')->responseDebug("获取好友列表 " . json_encode($data, JSON_UNESCAPED_UNICODE));
                }
                if (isset($data->data->list[0]->from_user)) {
                    $this->message($data->data->list[0]);
                    PadchatDi::getDefault()->get('log')->responseDebug("收到消息通知 " . json_encode($data, JSON_UNESCAPED_UNICODE));
                }

                break;
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
        switch ($data->msg_type) {
            case 5:
                {
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
    }

    /**
     * 异步回调处理
     * @param string $type
     * @param null $data
     */
    private function cmdRet(string $type, $data = null)
    {
        switch ($type) {
            case 'init':
                PadchatDi::getDefault()->get('api')->send('login', ['loginType' => 'qrcode']);
                break;
            case 'login':
                if ($data->data->success) {
                    PadchatDi::getDefault()->get('log')->responseDebug("获取二维码成功: {$data->data->msg}");
                }
                break;
        }
    }

}