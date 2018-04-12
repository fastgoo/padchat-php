<p align="center">
  Padchat-PHP-Demo
</p>
<p align="center">微信IPAD协议对接websocket服务,主要依赖swoole异步客户端，进程管理等功能模块</p>

<p align="center">
  <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg"></a> <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/swoole-1.9+-brightgreen.svg"></a> 
  <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/php->=7.0-brightgreen.svg"></a> <a href="https://github.com/fastgoo/padchat-php"><img src="https://img.shields.io/badge/server-windows-2077ff.svg"></a>
</p>

---

:zap: **[推荐--nodejs版本](https://github.com/binsee/padchat-sdk)：** padchat团队提供的nodesdk,本项目是参照该项目开发

:dart: **[Swoole](https://www.swoole.com/)：** 版本需要为1.9+，主要使用其定时器、异步websocket客户端、多进程管理等

:art: **[PHP7+](http://www.php.net/)：** PHP版本需要为7+，因为代码中使用PHP特有的行特性，低版本安装会出现异常

## 安装说明

需要在window-server启动server.exe应用程序，该程序目前仅供学习参考。

- `git clone https://github.com/fastgoo/padchat-php.git` 克隆项目
- `cd padchat-php` 进入项目
- `composer install` 安装依赖包
- `php run.php` 开始服务

## 项目说明
在启动项目之前需要先配置配置文件，配置后才方可启动

```
'debug' => [
        /** 请求记录debug */
        'request' => false,
        /** 响应数据记录debug */
        'response' => true,
        /** 在命令终端输出debug */
        'cmd' => true,
    ],
    'process' => [
        /** 启用多进程开启多个服务 */
        'status' => false,
        /** 最多同时启动的服务数量 */
        'count' => 2
    ],
    'server' => [
        'host' => '127.0.0.1',//server.exe所在的ip
        'port' => 7777, //端口
    ],
```
如需在生产环境运行，请关闭日志打印，以及终端输出。同时向保证进程稳定运行请使用进程守护工具守护进程，保证进程的正常运行。


## 简介
- QQ：773729704 记得备注github
- 微信：huoniaojugege 记得备注github



