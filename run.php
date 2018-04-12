<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2018/4/11
 * Time: 下午4:40
 */
include './vendor/autoload.php';

define('BASE_PATH', __DIR__);

use Padchat\Bootstrap;

$bootstrap = new Bootstrap();
$bootstrap->run();
