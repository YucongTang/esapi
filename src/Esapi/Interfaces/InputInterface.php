<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi\Interfaces;

/*
 * 输入处理接口类
 * */
interface InputInterface
{
    /*
     * 获取$_GET输入值
     * @$key 需要获取的指定键名
     * @$default 设置默认值
     * @xss 是否xss过滤 默认开启
     * */
    public function get($key = '' , $default = null , $xss = true);

    /*
     * 获取$_POST输入值
     * @$key 需要获取的指定键名
     * @$default 设置默认值
     * @xss 是否xss过滤 默认开启
     * */
    public function post($key = '' , $default = null , $xss = true);

    /*
     * 获取$_GET或$_POST输入值
     * @$key 需要获取的指定键名
     * @$default 设置默认值
     * @xss 是否xss过滤 默认开启
     * */
    public function any($key = '' , $default = null , $xss = true);

    /*
     * 判断是否为ajax
     * */
    public function isAjax();

    /*
     * 获取请求Method模式
     * */
    public function method();

    /*
     * 获取真实的请求IP
     * */
    public function ip($proxy = true);

    /*
     * 判断referer
     * */
    public function referrer($restrict = true, $allow = '');
}