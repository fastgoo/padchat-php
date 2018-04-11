<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/2/11
 * Time: 下午4:03
 */

namespace AsyncClient;

class Client
{
    use SwooleHttpClientApi;

    public function __construct(string $url, int $timeout = 5)
    {
        $this->initClient($url);
        $this->setClientConfig(['timeout' => $timeout]);
    }

    /**
     * 静态方法返回实例对象
     * @param string $url
     * @param int $timeout
     * @return Client
     */
    public static function init(string $url, int $timeout = 5)
    {
        return new self($url, $timeout);
    }

    /**
     * POST请求
     * @param string $url
     * @param array $params
     * @param callable $callback
     * @return $this
     */
    public function post(string $url, array $params, callable $callback)
    {
        $this->setRequest('POST', $url, $params, $callback);
        return $this;
    }

    /**
     * GET请求
     * @param string $url
     * @param callable $callback
     * @return $this
     */
    public function get(string $url, callable $callback)
    {
        $this->setRequest('GET', $url, [], $callback);
        return $this;
    }

    /**
     * PUT请求
     * @param string $url
     * @param array $params
     * @param callable $callback
     * @return $this
     */
    public function put(string $url, array $params, callable $callback)
    {
        $this->setRequest('PUT', $url, $params, $callback);
        return $this;
    }

    /**
     * DELETE请求
     * @param string $url
     * @param array $params
     * @param callable $callback
     * @return $this
     */
    public function delete(string $url, array $params, callable $callback)
    {
        $this->setRequest('DELETE', $url, $params, $callback);
        return $this;
    }

    public function send()
    {
        $this->sendRequest();
    }

    /**
     * 下载文件
     * @param string $url
     * @param string $path
     * @param callable $callback
     * @return $this
     */
    public function download(string $url, string $path, callable $callback)
    {
        $this->setDownloadRequest($url, $path, $callback);
        return $this;
    }

    /**
     * 设置IP，可绕过异步dns解析
     * @param string $ip
     * @return $this
     */
    public function setDnsIp(string $ip)
    {
        $this->setIp($ip);
        return $this;
    }

    /**
     * 设置请求头
     * @param array $params
     * @return $this
     */
    public function setHeaders(array $params)
    {
        $this->setRequestHeaders($params);
        return $this;
    }

    /**
     * 设置COOKIES
     * @param array $cookies
     * @return $this
     */
    public function setCookies(array $cookies)
    {
        $this->setRequestCookies($cookies);
        return $this;
    }

    /**
     * 设置配置
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->setClientConfig($config);
        return $this;
    }

    /**
     * 设置请求文件（POST）
     * @param array $params
     * @return $this
     */
    public function setFiles(array $params)
    {
        $this->setRequestFiles($params);
        return $this;
    }

    /**
     * 清除配置信息
     */
    public function clearAllSet()
    {
        $this->setRequestHeaders([]);
        $this->setRequestCookies([]);
        $this->setRequestFiles([]);
    }
}