<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/3/14
 * Time: 上午10:59
 */

namespace AsyncClient;

use Swoole\Async;
use Swoole\Http\Client as HttpClient;

trait SwooleHttpClientApi
{
    private $config = [];

    /**
     * 通过域名或者IP初始化客户端配置
     * @param string $url
     */
    private function initClient(string $url)
    {
        $parseArr = parse_url($url);
        $this->setHost($parseArr['host']);
        if (filter_var($parseArr['host'], FILTER_VALIDATE_IP)) {
            $this->setIp($parseArr['host']);
        }
        if ($parseArr['scheme'] == 'https') {
            $this->setScheme($parseArr['scheme']);
            $this->setPort(443);
        }
        if (!empty($parseArr['port'])) {
            $this->setPort($parseArr['port']);
        }
    }

    /**
     * 发送请求（域名需要解析dns获取IP地址，然后再请求）
     */
    private function sendRequest()
    {
        if ($this->getIp()) {
            foreach ($this->getRequestArr() as $v) {
                $this->requestMethod($v['method'], $v['url'], $v['param'], $v['call']);
            }
            foreach ($this->getDownloadArr() as $v) {
                $this->downloadFile($v['url'], $v['path'], $v['call']);
            }
        } else {
            Async::dnsLookup($this->getHost(), function ($host, $ip) {
                $this->setIp($ip);
                foreach ($this->getRequestArr() as $v) {
                    $this->requestMethod($v['method'], $v['url'], $v['param'], $v['call']);
                }
                foreach ($this->getDownloadArr() as $v) {
                    $this->downloadFile($v['url'], $v['path'], $v['call']);
                }
            });
        }
    }

    /**
     * 异步请求方法
     * @param string $method
     * @param string $url
     * @param array $data
     * @param callable $call
     */
    private function requestMethod(string $method, string $url, array $data, callable $call)
    {
        $client = new HttpClient($this->getIp(), $this->getPort(), $this->isHttps());
        /** 设置请求配置 */
        $this->getClientConfig() && $client->set($this->getClientConfig());
        /** 默认请求头 */
        $headers = [
            'Host' => $this->getHost(),
            "User-Agent" => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml,application/json',
            'Accept-Encoding' => 'gzip',
        ];
        $client->setHeaders(array_merge($this->getHeaders(), $headers));
        /** 设置cookie */
        $this->getCookies() && $client->setCookies($this->getCookies());
        /** 设置请求方法 GET POST PUT DELETE OPTIONS .. */
        $client->setMethod($method);
        /** 设置请求参数 */
        $data && $client->setData($data);
        /** 设置请求文件 */
        if (!empty($this->getFiles())) {
            foreach ($this->getFiles() as $k => $v) {
                $client->addFile($v, $k);
            }
            // 重置请求文件
            $this->setRequestFiles([]);
        }
        /** 开始请求 */
        $client->execute($url, function (HttpClient $client) use ($call) {
            $call($client);
            $client->close();
        });
    }

    /**
     * 下载文件
     * @param string $url
     * @param string $path
     * @param callable $call
     */
    private function downloadFile(string $url, string $path, callable $call)
    {
        $client = new HttpClient($this->getIp(), $this->getPort(), $this->isHttps());
        /** 设置请求配置 */
        $this->getClientConfig() && $client->set($this->getClientConfig());
        /** 默认请求头 */
        $headers = [
            'Host' => $this->getHost(),
            "User-Agent" => 'Chrome/49.0.2587.3',
            'Accept' => '*',
            'Accept-Encoding' => 'gzip',
        ];
        $client->setHeaders(array_merge($this->getHeaders(), $headers));
        /** 设置cookie */
        $this->getCookies() && $client->setCookies($this->getCookies());
        /** 下载操作 */
        $client->download($url, $path, function (HttpClient $client) use ($call) {
            $call($client);
            $client->close();
        });
    }

    /**
     * 是否是Https
     * @return bool
     */
    private function isHttps()
    {
        return isset($this->config['is_https']) ? $this->config['is_https'] : false;
    }

    /**
     * 获取Host
     * @return string
     */
    private function getHost()
    {
        return isset($this->config['host']) ? $this->config['host'] : '';
    }

    /**
     * 获取请求列表
     * @return array
     */
    private function getRequestArr()
    {
        return isset($this->config['request_arr']) ? $this->config['request_arr'] : [];
    }

    /**
     * 获取下载请求列表
     * @return array
     */
    private function getDownloadArr()
    {
        return isset($this->config['download_arr']) ? $this->config['download_arr'] : [];
    }

    /**
     * 获取请求文件配置
     * @return array
     */
    private function getFiles()
    {
        return isset($this->config['files']) ? $this->config['files'] : [];
    }

    /**
     * 获取IP
     * @return string
     */
    private function getIp()
    {
        return isset($this->config['ip']) ? $this->config['ip'] : '';
    }

    /**
     * 获取头配置
     * @return array
     */
    private function getHeaders()
    {
        return isset($this->config['headers']) ? $this->config['headers'] : [];
    }

    /**
     * 获取cookie配置
     * @return array
     */
    private function getCookies()
    {
        return isset($this->config['cookies']) ? $this->config['cookies'] : [];
    }

    /**
     * 获取请求接口
     * @return int
     */
    private function getPort()
    {
        return isset($this->config['port']) ? $this->config['port'] : 80;
    }

    /**
     * 获取swoole异步客户端配置
     * @return array
     */
    private function getClientConfig()
    {
        return isset($this->config['client_config']) ? $this->config['client_config'] : [];
    }

    /**
     * 设置swoole异步客户端配置
     * @param array $config
     */
    private function setClientConfig(array $config)
    {
        $this->config['client_config'] = $config;
    }

    /**
     * 设置下载请求配置
     * @param string $url
     * @param string $path
     * @param callable $call
     */
    private function setDownloadRequest(string $url, string $path, callable $call)
    {
        $this->config['download_arr'][] = ['url' => $url, 'path' => $path, 'call' => $call];
    }

    /**
     * 设置请求参数
     * @param string $method
     * @param string $url
     * @param array $param
     * @param callable $call
     */
    private function setRequest(string $method, string $url, array $param, callable $call)
    {
        $this->config['request_arr'][] = ['method' => $method, 'url' => $url, 'param' => $param, 'call' => $call];
    }

    /**
     * 设置IP（可避免异步DNS解析域名获取IP）
     * @param string $ip
     */
    private function setIp(string $ip)
    {
        $this->config['ip'] = $ip;
    }

    /**
     * 设置接口
     * @param int $port
     */
    private function setPort(int $port)
    {
        $this->config['port'] = $port;
    }

    /**
     * 设置加密状态
     * @param string $type
     */
    private function setScheme(string $type)
    {
        $this->config['is_https'] = $type == 'https' ? true : false;
    }

    /**
     * 设置HOST
     * @param string $host
     */
    private function setHost(string $host)
    {
        $this->config['host'] = $host;
    }

    /**
     * 设置请求headers
     * @param array $headers
     */
    private function setRequestHeaders(array $headers)
    {
        $this->config['headers'] = $headers;
    }

    /**
     * 设置请求文件
     * @param array $files
     */
    private function setRequestFiles(array $files)
    {
        $this->config['files'] = $files;
    }

    /**
     * 设置请求cookies
     * @param array $cookies
     */
    private function setRequestCookies(array $cookies)
    {
        $this->config['cookies'] = $cookies;
    }

}