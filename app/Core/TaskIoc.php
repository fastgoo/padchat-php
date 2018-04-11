<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午5:37
 */

namespace Padchat\Core;

class TaskIoc
{
    /** @var $_instance TaskIoc */
    private static $_instance = null;

    protected $taskArr = [];

    private function __construct()
    {

    }

    /**
     * 单例
     * @return TaskIoc
     */
    public static function getDefault()
    {
        if (!self::$_instance instanceof TaskIoc) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 注入依赖
     * @param string $name
     * @param $param
     */
    public function set(string $name, $param)
    {
        self::$_instance->taskArr[$name] = $param;
    }

    /**
     * 获取依赖
     * @param string $name
     * @return mixed|object
     */
    public function get(string $name)
    {
        if (!isset(self::$_instance->taskArr[$name])) {
            return [];
        }
        return self::$_instance->taskArr[$name];
    }
}