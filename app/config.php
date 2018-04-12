<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/12
 * Time: 下午3:57
 */
return [
    'debug' => [
        /** 请求记录debug */
        'request' => true,
        /** 响应数据记录debug */
        'response' => true,
        /** 在命令终端输出debug */
        'cmd' => true,
    ],
    'process' => [
        /** 启用多进程开启多个服务 */
        'status' => false,
        /** 最多同时启动的服务数量 */
        'count' => 10
    ],
    'server' => [
        'host' => '118.89.159.190',
        'port' => 7777,
    ],
];