<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午2:01
 */

namespace AsyncClient;

use Swoole\Process;

class SwooleProcess
{
    /** @var $process Process */
    private $process;
    /** @var $work Process */
    private $work;
    /** @var $pid pid */
    private $pid;

    public function __construct()
    {

    }

    /**
     * 初始化
     * @param callable $call
     */
    public function init(callable $call)
    {
        $this->process = new Process(function (Process $work) use ($call) {
            $this->work = $work;
            $call($work);
        });
    }

    /**
     * 设置进程名称
     * @param string $name
     */
    public function setProcessName(string $name)
    {
        $this->process->name($name);
    }

    /**
     * 设置守护进程
     * @param bool $is_daemon
     */
    public function setDaemon(bool $is_daemon)
    {
        $this->process::daemon($is_daemon);
    }

    /**
     * 结束进程
     */
    public function kill()
    {
        if (!Process::kill($this->pid, 0)) {
            echo $this->pid;
            echo $this->work->exit();
        }
    }

    /**
     * 自动等待回收进程
     */
    public function wait()
    {
        Process::signal(SIGCHLD, function ($sig) {
            while ($ret = Process::wait(false)) {
                echo "PID={$ret['pid']}\n";
            }
        });
    }

    /**
     * 运行进程服务
     * @return int
     */
    public function run()
    {
        $this->pid = $this->process->start();
        return $this->pid;
    }
}