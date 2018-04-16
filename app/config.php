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
        'request' => false,
        /** 响应数据记录debug */
        'response' => false,
        /** 在命令终端输出debug */
        'cmd' => true,
    ],
    'process' => [
        /** 启用多进程开启多个服务 */
        'status' => false,
        /** 最多同时启动的服务数量 */
        'count' => 1
    ],
    'server' => [
        'host' => '47.98.198.224',
        'port' => 7777,
        'cache' => true,
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'auth' => '',
    ],
];