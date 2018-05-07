<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/16
 * Time: 上午10:30
 */

namespace Padchat;

use Padchat\Core\TaskIoc;

class Client
{
    /** @var $redis \Redis */
    private $redis;

    public function __construct($config)
    {
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
        !empty($config['auth']) && $this->redis->auth($config['auth']);
    }

    /**
     * 获取登录二维码base64编码
     * @return array|mixed
     */
    public function getLoginQrcode()
    {
        return $this->redis->get("padchat_wx_login_qrcode_base64");
    }

    /**
     * 获取正在登录的信息
     * @return array|mixed
     */
    public function getLoginStatus()
    {
        $str = $this->redis->get("padchat_wx_login_status");
        return $str ? json_decode($str, true) : [];
    }

    /**
     * 获取登录成功的信息
     * @return array|mixed
     */
    public function getLoginSuccessInfo()
    {
        $str = $this->redis->get("padchat_wx_success_info");
        return $str ? json_decode($str, true) : [];
    }

    /**
     * 获取微信信息
     * @param string $wxid
     * @return array|mixed
     */
    public function getWxInfo(string $wxid)
    {
        $str = $this->redis->get("padchat_wx_{$wxid}_info");
        return $str ? json_decode($str, true) : [];
    }

    /**
     * 获取好友列表
     * @param string $wxid
     * @param int $type 1好友 2群聊 3公众号
     * @param int $page
     * @param int $nums
     * @return array|bool
     */
    public function getFriendList(string $wxid, int $type = 1, int $page = 1, int $nums = 15)
    {
        $type == 1 && $key = "padchat_wx_{$wxid}_friend_list";
        $type == 2 && $key = "padchat_wx_{$wxid}_group_list";
        $type == 3 && $key = "padchat_wx_{$wxid}_gh_list";

        $rows = $this->redis->lRange($key, ($page - 1) * $nums, $page * $nums);
        $data = [];
        foreach ($rows as $val) {
            $data[] = json_decode($val, true);
        }
        return $data;
    }

    /**
     * 发送消息
     * @param string $user
     * @param string $wxid
     * @param $content
     * @param array $at_list
     * @return int
     */
    public function sendMsg(string $user, string $wxid, $content, array $at_list = [])
    {
        return $this->redis->rPush("padchat_wx_{$user}_send_msg_task", json_encode(compact('wxid', 'content', 'at_list')));
    }

    /**
     * 获取消息列表
     * @param string $wxid
     * @param int $page
     * @param int $nums
     * @return array|bool
     */
    public function getMessageList(string $wxid, int $page = 1, int $nums = 15)
    {
        $rows = $this->redis->lRange("padchat_wx_{$wxid}_msg_list", ($page - 1) * $nums, $page * $nums);
        $data = [];
        foreach ($rows as $val) {
            $data[] = json_decode($val, true);
        }
        return $data;
    }
}