<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi\Console;

use Esapi\Esapi;

/*
 * 所有控制器继承类
 * */
class Application
{
    public function __get($name)
    {
        return Esapi::instance()->{$name};
    }
}