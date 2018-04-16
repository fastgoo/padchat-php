<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/12
 * Time: 上午8:38
 */

namespace Padchat\Core;

use Padchat\Core\Ioc as PadchatDi;

class Receive
{
    protected $param;

    public function __construct()
    {

    }

    /**
     * 设置请求参数
     * @param $params
     */
    public function setParams($params)
    {
        $this->param = $params;
    }

    /**
     * 获取请求参数
     * @return mixed
     */
    public function getParams()
    {
        return $this->param;
    }

    /**
     * 获取cmdId类型
     * @return mixed|object|string
     */
    private function getCmdIdType()
    {
        if (empty($this->param->cmdId)) {
            return '';
        }
        return TaskIoc::getDefault()->get($this->param->cmdId);
    }

    /**
     * 是否实例化成功（事件）
     * @return bool
     */
    public function isInit()
    {
        if (!$this->param) {
            return false;
        }
        return $this->getCmdIdType() == 'init' && $this->param->type == 'cmdRet';
    }

    /**
     * 获取登录二维码成功（事件）
     * @return array|bool
     */
    public function isGetQrcodeSuccess()
    {
        if (!$this->param) {
            return false;
        }
        return isset($this->param->event) && $this->param->event == 'qrcode' && !empty($this->param->data->qr_code) ? $this->param->data : false;
    }

    /**
     * 获取登录二维码成功（事件）
     * @return array|bool
     */
    public function isLoginWarning()
    {
        if (!$this->param) {
            return false;
        }
        if ($this->getCmdIdType() == 'login' && $this->param->data->success == false) {
            $xml = strstr($this->param->data->data->message, "http");
            $url = strstr($xml, "]]></Url>", true);
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $url;
            }
        }
        return false;
    }

    /**
     * 是否等待扫码（事件）
     * @return bool
     */
    public function isWaitScan()
    {
        if (!$this->param) {
            return false;
        }
        return isset($this->param->event) && $this->param->type == 'userEvent' && $this->param->event == 'scan' && isset($this->param->data->status) && $this->param->data->status == 0 && empty($this->param->data->user_name) ? $this->param->data : false;
    }

    /**
     * 是否等待确认
     * @return bool
     */
    public function isWaitConfirm()
    {
        if (!$this->param) {
            return false;
        }
        return isset($this->param->event) && $this->param->type == 'userEvent' && $this->param->event == 'scan' && isset($this->param->data->status) && $this->param->data->status == 1 && empty($this->param->data->user_name) ? $this->param->data : false;
    }

    /**
     * 是否登录成功
     * @return bool
     */
    public function isLoginSuccess()
    {
        if (!$this->param) {
            return false;
        }
        return isset($this->param->event) && $this->param->type == 'userEvent' && $this->param->event == 'scan' && !empty($this->param->data->user_name) ? $this->param->data : false;
    }

    /**
     * 是否消息事件回调
     * @return bool
     */
    public function isMessageCallback()
    {
        if (!$this->param) {
            return false;
        }
        return isset($this->param->event) && $this->param->event == 'push' && isset($this->param->data->list[0]->from_user) ? $this->param->data->list[0] : false;
    }

    /**
     * 是否是获取好友列表回调
     * @return bool
     */
    public function isGetFriendsList()
    {
        if (!$this->param) {
            return false;
        }
        return isset($this->param->event) && $this->param->event == 'push' && isset($this->param->data->list[1]->big_head) ? $this->param->data->list : false;
    }

    /**
     * 是否是@我
     * @return bool
     */
    public function isAtMe()
    {
        if (!empty($this->param->data->list[0]->msg_source)) {
            $sourceRet = json_decode(json_encode(simplexml_load_string($this->param->data->list[0]->msg_source)));
            if (!empty($sourceRet->atuserlist) && strpos($sourceRet->atuserlist, TaskIoc::getDefault()->get('wxid')) !== false) {
                return true;
            }
        }
        return false;
    }

    public function getMsgType()
    {
        if (empty($this->param->data->list[0]->sub_type)) {
            return '';
        }
    }

    /**
     * 获取好友申请的参数
     * @param $params
     * @return array
     */
    public function getFriendRequestParams($params): array
    {
        $data = json_decode(json_encode(simplexml_load_string($params->content)), true);
        if (empty($data['@attributes'])) {
            return [];
        }
        return $data['@attributes'];
    }

    /**
     * 获取已登录的用户的wxId
     * @return mixed|object
     */
    public function getWxId()
    {
        return TaskIoc::getDefault()->get('wxid');
    }

    /**
     * 获取消息数据，优化处理
     * @param $params
     * @return array
     */
    public function getMessageParams($params): array
    {
        $data = (array)$params;
        /** 判断消息是群消息还是好友消息 */
        if (strpos($data['from_user'], '@chatroom') !== false) {
            list($send_wxid) = explode(':', $data['content']);
            $content = str_replace(["$send_wxid:", "\n", "\t"], '', $data['content']);
            if (strpos($content, '</') !== false) {
                $xml_params = json_decode(json_encode(simplexml_load_string($content)), true);
            }
            $data['msg_from'] = 2;
        } else {
            $send_wxid = $data['from_user'];
            $content = $data['content'];
            $data['msg_from'] = 1;
        }
        $data['send_wxid'] = $send_wxid;
        $data['content'] = $content;
        $data['xml_params'] = !empty($xml_params) ? $xml_params : [];
        return $data;
    }
}