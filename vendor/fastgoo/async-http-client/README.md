# 基于Swoole扩展的HTTP异步客户端

[![Latest Version](https://img.shields.io/badge/release-v1.0.0-green.svg?maxAge=2592000)](https://github.com/fastgoo/AsyncHttpClient/releases)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg?maxAge=2592000)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=1.9-brightgreen.svg?maxAge=2592000)](https://github.com/swoole/swoole-src)

# 简介
基于 Swoole 异步客户端的扩展包，可以像使用 GuzzleHttp 简单优雅的使用swoole的异步客户端，无需关注底层实现以及处理逻辑。可实现同时发起N个HTTP请求不会被阻塞。经测试循环并发100个请求，全部返回结果只需要3-4秒的时间。

- 基于 Swoole 扩展
- 需在CLI模式下运行
- 支持HTTPS 与 HTTP 2种协议
- 使用 HTTPS 必须在编译swoole时启用--enable-openssl
- 解决高并发请求（可做接口压测）
- 异步 HTTP 请求，非阻塞
- 该扩展适用于目前所有现代框架
- 作者qq 773729704、微信 huoniaojugege  加好友备注github

# 更新记录
- 2018.03.30 去掉get请求的参数，参数让用户自己带在URL上

# 参考文档
[**中文文档**](https://wiki.swoole.com/wiki/page/p-http_client.html)

# 环境要求

1. PHP 7.0 +
2. [Swoole 1.9](https://github.com/swoole/swoole-src/releases) +
3. [Composer](https://getcomposer.org/)

# Composer 安装

* `composer require fastgoo/async-http-client`


# 使用范例

```
# ----------HTTP 请求（请求方式 get post put delete）--------
$client = new \AsyncClient\Client("https://open.fastgoo.net");
$params = [
    'address' => '邮箱',
    'subject' => '标题',
    'body' => '内容',
];

# 单请求
$client->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
    var_dump($client->body);
});

# 多请求
foreach (range(1, 50) as $v) {
    $client->post('/base.api/email/send', $params, function (\Swoole\Http\Client $client) {
        var_dump($client->body);
    });
}

# 发送请求文件
$client->setFiles(['files' => __DIR__ . '/fastgoo-logo.png'])->post('/base.api/file/upload',[],function (\Swoole\Http\Client $client) {
    var_dump($client->body);
})

# 开始发送请求
$client->send();
# -----------------------------END--------------------------



# 下载文件范例
$client = new \AsyncClient\Client("https://timgsa.baidu.com");
$client->download('/timg?image&quality=80&size=b9999_10000&sec=1521617943&di=913c0898b55cf2992d6d5136013e98d2&imgtype=jpg&er=1&src=http%3A%2F%2Fimg.taopic.com%2Fuploads%2Fallimg%2F120727%2F201995-120HG1030762.jpg', './logoaa.png', function (\Swoole\Http\Client $client) {
    var_dump($client);
});
$client->send();


# 静态对象实例化请求
\AsyncClient\Client::init("https://open.fastgoo.net")->post('/base.api/email/send', [], function (\Swoole\Http\Client $client) {
    var_dump($client->body);
})->send();

# 请求设置Cookies 和 Headers
$client->setCookies([]);
$client->setHeaders([]);

# 重点强调，由于默认不设置ip会自动异步解析DNS取到IP然后才能创建客户端，
# 异步解析DNS需要在CLI的模式下才能使用
# 如果条件允许的情况下尽量存取IP然后去发起请求，不然调外部网络会造成请求阻塞，性能优势会有所下降
$client->setDnsIp('192.168.1.1');

```


