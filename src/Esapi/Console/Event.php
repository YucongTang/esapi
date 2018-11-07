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
use Esapi\Interfaces\EventInterface;

/*
 * 事件处理类
 * */
class Event
{
    /*
     * 事件回调方法
     * */
    const CALLBACK_METHOD = 'handle';

    /*
     * 事件触发方法
     * @location 事件触发地址
     * @args 事件传递参数
     * */
    static function execute( $location ,$args = null )
    {
        /*
         * 是否有已启动监听的事件
         * */
        if (!$Event = array_filter(Esapi::config("event.{$location}")))
        {
            /*
             * 无事件需要触发,跳过事件处理
             * */
            goto BackResult;
        }
        /*
         * 有时间需要触发,开始处理事件
         * */
        foreach ($Event as $class=>$message)
        {
            /*
             * 事件是否为可回调类
             * */
            if(!class_exists($class))
            {
                /*
                 * 抛出事件非可回调类异常
                 * */
                throw new \Exception(sprintf (
                    "Event %s is not found",$class
                ));
            }
            /*
             * 实例化事件
             * */
            $object = new $class;
            /*
             * 事件是否继承指定接口
             * */
            if(!$object instanceof EventInterface)
            {
                /*
                 * 未继承接口，抛出异常
                 * */
                throw new \Exception(sprintf (
                    "Event %s must inherit interfaces",$class
                ));
            }
            /*
             * 回调事件,如果返回false,抛出设置的默认错误
             * */
            if(call_user_func([$object,static::CALLBACK_METHOD],$args) === false)
            {
                raise($message,600);
            }
        }
        /*
         * 事件执行完毕,继续执行其他操作
         * */
        BackResult:
        return Esapi::instance();
    }
}