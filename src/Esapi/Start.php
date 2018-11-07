<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/
/*
 * 定义框架运行起始时间（不建议重定义）
 * */
define ('ES_START',microtime (true));

/*
 * 定义框架运行模式
 * */
define('ES_CLI',PHP_SAPI == 'cli' ? true : false);

/*
 * 定义框架分隔符（不建议重定义）
 * */
define ('ES_SEP',DIRECTORY_SEPARATOR);

/*
 * 定义框架项目运行目录（可被重定义）
 * */
defined('ES_APP') || define ('ES_APP',(ES_CLI ? dirname(getcwd()) : getcwd ()) .ES_SEP);

/*
 * 定义框架核心文件目录
 * */
define ('ES_API',__DIR__.ES_SEP);

/*
 * 定义Composer扩展包目录
 * */
define('ES_VEN',dirname(dirname (ES_API)).ES_SEP.'vendor'.ES_SEP);

/*
 * 设置安全目录
 * */
ini_set('open_basedir',implode(':',[
    ini_get('open_basedir'),ES_API,ES_APP,ES_VEN
]));

/*
 * 载入Composer自动加载文件
 * */
$object = require_once ES_VEN."autoload.php";

/*
 * 框架运行Debug监听
 * */
\Esapi\Debug\Debug::listen();

/*
 * 生成本次链接唯一hash
 * */
define('ES_HASH',\Esapi\Esapi::hash(\Esapi\Esapi::config('app.hash_digits')?:16));

/*
 * 设置应用运行默认时区
 * */
date_default_timezone_set(
    \Esapi\Esapi::config ('default.time_zone')
);

/*
 * 项目运行并返回结果
 * */
if(ES_CLI === false)
{
    \Esapi\Esapi::instance()->initialize(function(){
        return \Esapi\Console\Event::execute('ROUTE_BEFORE');
    } , $object)->run();
}