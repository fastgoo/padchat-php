<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午4:41
 */

namespace Padchat\Core;

use Padchat\Core\Ioc as PadchatDi;

class Api
{
    public function send(string $cmd, array $data = [])
    {
        $cmdId = uniqid();
        TaskIoc::getDefault()->set($cmdId, $cmd);
        TaskIoc::getDefault()->set('lat-cmd-id', $cmdId);
        $data = ['type' => 'user', 'cmd' => $cmd, 'cmdId' => $cmdId, 'data' => $data];
        $ret = json_encode($data, JSON_UNESCAPED_UNICODE);
        $config = PadchatDi::getDefault()->get('config');
        if ($config->debug->cmd) {
            echo "\n请求：$ret";
        }
        if ($config->debug->request) {
            PadchatDi::getDefault()->get('log')->requestDebug($ret);
        }
        PadchatDi::getDefault()->get('websocket')->send($ret);
    }

    /**
     * 实例化微信客户端
     */
    public function init()
    {
        $this->send('init');
    }

    /**
     * 登录（没有token信息会自动授权登录）
     * @param array $data
     */
    public function login(array $data = [])
    {
        if (!$data) {
            $this->send('login', ['loginType' => 'qrcode']);
            return;
        }
        !empty($data['token']) && $data['loginType'] = 'request';
        !empty($data['username']) && $data['loginType'] = 'user';
        $this->send('login', $data);
    }

    /**
     * 获取微信设备信息
     */
    public function getWxData()
    {
        $this->send('getWxData');
    }

    /**
     * 获取登录token
     */
    public function getLoginToken()
    {
        $this->send('getLoginToken');
    }

    /**
     * 发送消息
     * 发送APP消息(链接) {title,des,url,thumburl}
     * 分享名片
     * @param string $user
     * @param $content
     * @param array $atList
     */
    public function sendMsg(string $user, $content, array $atList = [])
    {
        if (is_string($content)) {
            $this->send('sendMsg', ['toUserName' => $user, 'content' => $content, 'atList' => $atList]);
        } else if (!empty($content['title'])) {
            $this->send('sendAppMsg', ['toUserName' => $user, 'content' => $content]);
        } else if (!empty($content['image'])) {
            $this->send('sendImage', ['toUserName' => $user, 'file' => $content['image']]);
        } else if (!empty($content['voice'])) {
            $this->send('sendVoice', ['toUserName' => $user, 'file' => $content['voice']]);
        } else if (!empty($content['userId']) && isset($content['content'])) {
            $this->send('shareCard', ['toUserName' => $user, 'content' => $content['content'], 'userId' => $content['userId']]);
        }
    }

    /**
     * 获取原始文件(图片、语音、视频)
     * @param $body
     */
    public function getMsgFile($body)
    {
        !empty($body['image']) && $this->send('getMsgImage', ['rawMsgData' => $body]);
        !empty($body['voice']) && $this->send('getMsgVoice', ['rawMsgData' => $body]);
        !empty($body['video']) && $this->send('getMsgVideo', ['rawMsgData' => $body]);
    }

    /**
     * 获取联系人信息
     * @param string $userId
     */
    public function getContact(string $userId)
    {
        $this->send('getContact', compact('userId'));
    }

    /**
     * 搜索联系人(可查看非好友)
     * @param string $userId
     */
    public function searchContact(string $userId)
    {
        $this->send('searchContact', compact('userId'));
    }

    /**
     * 删除好友
     * @param string $userId
     */
    public function deleteContact(string $userId)
    {
        $this->send('deleteContact', compact('userId'));
    }

    /**
     * 获取二维码图片
     * @param string $userId
     * @param int $style
     */
    public function getContactQrcode(string $userId, int $style = 1)
    {
        $this->send('getContactQrcode', compact('userId', 'style'));
    }

    /**
     * 同意好友申请
     * @param string $stranger
     * @param string $ticket
     */
    public function acceptUser(string $stranger, string $ticket)
    {
        $this->send('acceptUser', compact('stranger', 'ticket'));
    }

    /**
     * 添加联系人
     * @param string $stranger
     * @param string $ticket
     * @param int $type
     * @param string $content
     */
    public function addContact(string $stranger, string $ticket, int $type = 3, string $content = '')
    {
        $this->send('acceptUser', compact('stranger', 'ticket', 'type', 'content'));
    }

    /**
     * 附近的人打招呼
     * @param string $stranger
     * @param string $ticket
     * @param string $content
     */
    public function sayHello(string $stranger, string $ticket, string $content = '')
    {
        $this->send('acceptUser', compact('stranger', 'ticket', 'content'));
    }

    /**
     * 设置备注
     * @param string $userId
     * @param string $remark
     */
    public function setRemark(string $userId, string $remark)
    {
        $this->send('setRemark', compact('userId', 'remark'));
    }

    /**
     * 修改头像
     * @param string $base64
     */
    public function setHeadImg(string $base64)
    {
        $this->send('setHeadImg', ['file' => $base64]);
    }

    /**
     * 创建群聊 （群功能）
     * @param array $userList
     */
    public function createRoom(array $userList)
    {
        $this->send('createRoom', ['userList' => $userList]);
    }

    /**
     * 获取群成员（群功能）
     * @param string $groupId
     */
    public function getRoomMembers(string $groupId)
    {
        $this->send('getRoomMembers', ['groupId' => $groupId]);
    }

    /**
     * 添加邀请群成员（群功能）
     * @param string $groupId
     * @param string $userId
     * @param int $type
     */
    public function addRoomMember(string $groupId, string $userId, int $type = 1)
    {
        $type == 1 ? $this->send('inviteRoomMember', ['groupId' => $groupId, 'userId' => $userId]) : $this->send('addRoomMember', ['groupId' => $groupId, 'userId' => $userId]);
    }

    /**
     * 删除群成员（群功能）
     * @param string $groupId
     * @param string $userId
     */
    public function deleteRoomMember(string $groupId, string $userId)
    {
        $this->send('deleteRoomMember', ['groupId' => $groupId, 'userId' => $userId]);
    }

    /**
     * 退出群聊（群功能）
     * @param string $groupId
     */
    public function quitRoom(string $groupId)
    {
        $this->send('deleteRoomMember', ['groupId' => $groupId]);
    }

    /**
     * 设置群公告（群功能）
     * @param string $groupId
     * @param string $content
     */
    public function setRoomAnnouncement(string $groupId, string $content)
    {
        $this->send('setRoomAnnouncement', ['groupId' => $groupId, 'content' => $content]);
    }

    /**
     * 设置群名称（群功能）
     * @param string $groupId
     * @param string $name
     */
    public function setRoomName(string $groupId, string $name)
    {
        $this->send('setRoomName', ['groupId' => $groupId, 'content' => $name]);
    }

    /**
     * 获取群聊二维码（群功能）
     * @param string $groupId
     */
    public function getRoomQrcode(string $groupId)
    {
        $this->send('getRoomQrcode', ['groupId' => $groupId]);
    }

    /**
     * 上传图片到朋友圈（朋友圈）
     * @param string $file
     */
    public function snsUpload(string $file)
    {
        $this->send('snsUpload', compact('file'));
    }

    /**
     * 操作（朋友圈）
     * @param $momentId 朋友圈信息id
     * @param $type 操作类型，1为删除朋友圈，4为删除评论，5为取消赞
     * @param $commentId 操作类型，当type为4时，对应删除评论的id，其他状态为0
     * @param int $commentType 操作类型，当删除评论时可用，需与评论type字段一致
     */
    public function snsObjectOp($momentId, $type, $commentId, $commentType = 2)
    {
        $this->send('snsObjectOp', compact('momentId', 'type', 'commentId', 'commentType'));
    }

    /**
     * 发送朋友圈（朋友圈）
     * @param string $content
     */
    public function snsSendMoment(string $content)
    {
        $this->send('snsSendMoment', compact('content'));
    }

    /**
     * 查看用户朋友圈
     * @param string $userId
     * @param int $momentId
     */
    public function snsUserPage(string $userId, int $momentId = 0)
    {
        $this->send('snsUserPage', compact('userId', 'momentId'));
    }

    /**
     * 查看朋友圈动态（朋友圈）
     * @param int $momentId
     */
    public function snsTimeline(int $momentId = 0)
    {
        $this->send('snsTimeline', compact('momentId'));
    }

    /**
     * 获取朋友圈信息详情（朋友圈）
     * @param int $momentId
     */
    public function snsGetObject(int $momentId)
    {
        $this->send('snsGetObject', compact('momentId'));
    }

    /**
     * 朋友圈评论（朋友圈）
     * @param string $userId
     * @param int $momentId
     * @param string $content
     */
    public function snsComment(string $userId, int $momentId, string $content)
    {
        $this->send('snsComment', compact('userId', 'momentId', 'content'));
    }

    /**
     * 朋友圈点赞（朋友圈）
     * @param $userId
     * @param $momentId
     */
    public function snsLike($userId, $momentId)
    {
        $this->send('snsLike', compact('userId', 'momentId'));
    }

    /**
     * 同步收藏（收藏）
     * @param string $favKey
     */
    public function syncFav(string $favKey = '')
    {
        $this->send('syncFav', compact('favKey'));
    }

    /**
     * 添加收藏（收藏）
     * @param string $content
     */
    public function addFav(string $content)
    {
        $this->send('addFav', compact('content'));
    }

    /**
     * 获取收藏信息（收藏）
     * @param int $favId
     */
    public function getFav(int $favId = 0)
    {
        $this->send('getFav', compact('favId'));
    }

    /**
     * 删除收藏（收藏）
     * @param int $favId
     */
    public function deleteFav(int $favId = 0)
    {
        $this->send('deleteFav', compact('favId'));
    }

    /**
     * 获取标签列表
     */
    public function getLabelList()
    {
        $this->send('getLabelList');
    }

    /**
     * 新增标签
     * @param string $label
     */
    public function addLabel(string $label)
    {
        $this->send('addLabel', compact('label'));
    }

    /**
     * 删除标签
     * @param int $labelId
     */
    public function deleteLabel(int $labelId)
    {
        $this->send('deleteLabel', compact('labelId'));
    }

    /**
     * 给用户设置标签
     * @param string $userId
     * @param int $labelId
     */
    public function setLabel(string $userId, int $labelId)
    {
        $this->send('setLabel', compact('userId', 'labelId'));
    }

    /**
     * 获取转账信息
     * @param string $rawMsgData
     */
    public function queryTransfer(string $rawMsgData)
    {
        $this->send('queryTransfer', compact('rawMsgData'));
    }

    /**
     * 接受转账
     * @param string $rawMsgData
     */
    public function acceptTransfer(string $rawMsgData)
    {
        $this->send('acceptTransfer', compact('rawMsgData'));
    }

    /**
     * 获取红包信息
     * @param string $rawMsgData
     * @param int $index
     */
    public function queryRedPacket(string $rawMsgData, int $index = 0)
    {
        $this->send('queryRedPacket', compact('rawMsgData', 'index'));
    }

    /**
     * 接受红包
     * @param string $rawMsgData
     */
    public function receiveRedPacket(string $rawMsgData)
    {
        $this->send('receiveRedPacket', compact('rawMsgData'));
    }

    /**
     * 打开红包
     * @param string $rawMsgData
     * @param $key
     */
    public function openRedPacket(string $rawMsgData, $key)
    {
        $this->send('openRedPacket', compact('rawMsgData', 'key'));
    }

    /**
     * 搜索公众号
     * @param string $content
     */
    public function searchMp(string $content)
    {
        $this->send('searchMp', compact('content'));
    }

    /**
     * 获取公众号信息
     * @param string $userId
     */
    public function getSubscriptionInfo(string $userId)
    {
        $this->send('getSubscriptionInfo', ['ghName' => $userId]);
    }

    /**
     * 操作公众号
     * @param string $ghName
     * @param $menuId
     * @param $menuKey
     */
    public function operateSubscription(string $ghName, $menuId, $menuKey)
    {
        $this->send('operateSubscription', compact('ghName', 'menuId', 'menuKey'));
    }

    /**
     * 获取连接请求token
     * @param string $ghName
     * @param string $url
     */
    public function getRequestToken(string $ghName, string $url)
    {
        $this->send('getRequestToken', compact('ghName', 'url'));
    }

    /**
     * 请求页面
     * @param $url
     * @param $xKey
     * @param $xUin
     */
    public function requestUrl($url, $xKey, $xUin)
    {
        $this->send('requestUrl', compact('url', 'xKey', 'xUin'));
    }

}