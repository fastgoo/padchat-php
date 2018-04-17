<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/14
 * Time: 下午12:06
 */

namespace Padchat\Core;

use Padchat\Core\Ioc as PadchatDi;

class Client
{
    /** @var $redis \Redis */
    private $redis;
    private $config;

    public function __construct()
    {
        $this->redis = PadchatDi::getDefault()->get('redis');
        $this->config = PadchatDi::getDefault()->get('config');
    }

    /**
     * 设置登录二维码base64编码
     * @param string $base64
     * @return bool
     */
    public function setLoginQrcode(string $base64)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        return $this->redis->set('padchat_wx_login_qrcode_base64', $base64, 300);
    }


    /**
     * 设置登录状态
     * @param array $data
     * @return bool
     */
    public function setLoginStatus($data)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        return $this->redis->set("padchat_wx_login_status", is_string($data) ? $data : json_encode($data), 60);
    }

    /**
     * 设置登录成功的信息
     * @param array $data
     * @return bool
     */
    public function setLoginSuccessInfo($data)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        return $this->redis->set("padchat_wx_success_info", is_string($data) ? $data : json_encode($data), 300);
    }

    /**
     * 设置微信信息
     * @param string $wxid
     * @param array $data
     * @return bool
     */
    public function setWxInfo(string $wxid, $data)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        return $this->redis->set("padchat_wx_{$wxid}_info", is_string($data) ? $data : json_encode($data), 3600 * 24 * 30);
    }

    /**
     * 设置好友列表
     * @param string $wxid
     * @param $data
     * @return bool
     */
    public function setFriendList(string $wxid, $data)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        foreach ($data as $val) {
            if (!empty($val->user_name)) {
                if (strpos($val->user_name, 'gh_') !== false) {
                    $this->redis->rPush("padchat_wx_{$wxid}_gh_list", json_encode($val));
                } elseif (strpos($val->user_name, '@chatroom') !== false) {
                    $this->redis->rPush("padchat_wx_{$wxid}_group_list", json_encode($val));
                } else {
                    $this->redis->rPush("padchat_wx_{$wxid}_friend_list", json_encode($val));
                }
            }
        }
    }

    /**
     * 设置成员列表
     * @param string $wxid
     * @param string $group_id
     * @param $data
     * @return bool
     */
    public function setMemberList(string $wxid, string $group_id, $data)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        foreach ($data as $val) {
            if (!empty($val->user_name)) {
                $this->redis->rPush("padchat_wx_{$wxid}_group_{$group_id}_member_list", json_encode($val));
            }
        }
    }

    /**
     * 发送消息
     * @param string $wxid
     * @param $content
     * @param array $at_list
     * @return int
     */
    public function sendMsg(string $wxid, $content, array $at_list = [])
    {
        if (!$this->config->server->cache) {
            return false;
        }
        return $this->redis->rPush("padchat_wx_" . TaskIoc::getDefault()->get('wxid') . "_send_msg_task", json_encode(compact('wxid', 'content', 'at_list')));
    }

    /**
     *
     * @param string $wxid
     * @return string
     */
    public function msgTask(string $wxid)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        $key = "padchat_wx_{$wxid}_send_msg_task";
        if ($this->redis->exists($key) && $this->redis->lLen($key)) {
            $ret = json_decode($this->redis->lPop($key), true);
            if ($ret) {
                PadchatDi::getDefault()->get('api')->sendMsg($ret['wxid'], $ret['content'], $ret['at_list']);
            }
        }
    }

    /**
     * 设置消息列表
     * 用户外部获取消息列表记录的
     * @param string $wxid
     * @param $message
     * @return int
     */
    public function setMessageList(string $wxid, $message)
    {
        if (!$this->config->server->cache) {
            return false;
        }
        return $this->redis->rPush("padchat_wx_{$wxid}_msg_list", json_encode($message));
    }
}