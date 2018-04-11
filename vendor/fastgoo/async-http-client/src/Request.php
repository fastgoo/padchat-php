<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/3/14
 * Time: 下午3:49
 */
namespace AsyncClient;

class Request
{
    use SwooleHttpClientApi;


    private function request(string $method,string $url,array $param,callable $call)
    {
        $this->setRequest($method, $url, $params, $callback);
    }



}