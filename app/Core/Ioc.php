<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午5:11
 */

namespace Padchat\Core;

class Ioc
{
    /** @var $_instance Ioc */
    private static $_instance = null;
    protected $icoArr = [];

    private function __construct()
    {

    }

    /**
     * 单例
     * @return Ioc
     */
    public static function getDefault()
    {
        if (!self::$_instance instanceof Ioc) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 注入依赖
     * @param string $name
     * @param callable $call
     */
    public function set(string $name, callable $call)
    {
        self::$_instance->icoArr[$name] = $call();
    }

    /**
     * 获取依赖
     * @param string $name
     * @return mixed|object
     */
    public function get(string $name)
    {
        if (!isset(self::$_instance->icoArr[$name])) {
            return (object)[];
        }
        return self::$_instance->icoArr[$name];
    }

}