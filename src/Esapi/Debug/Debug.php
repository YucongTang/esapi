<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi\Debug;

use Esapi\Esapi;

/*
 * Debug类
 * */
class Debug extends Utils\DebugHandle
{
    /*
     * 监听应用错误
     * */
    static function listen()
    {
        if(empty(Esapi::config('dev.ip')))
        {
            define('ES_DEBUG',Esapi::config('dev.debug'));
        }
        else
        {
            if(
                is_string(Esapi::config('dev.ip')) &&
                Esapi::config('dev.ip') === Esapi::instance()->request->getServer('server_addr')
            )
            {
                define('ES_DEBUG',Esapi::config('dev.debug'));
            }
            elseif(
                is_array(Esapi::config('dev.ip')) &&
                in_array(Esapi::instance()->request->getServer('server_addr'),Esapi::config('dev.ip'),true)
            )
            {
                define('ES_DEBUG',Esapi::config('dev.debug'));
            }
            else
            {
                define('ES_DEBUG',false);
            }
        }
        if(ES_DEBUG)
        {
            ini_set('display_errors',1);
        }
        else
        {
            ini_set('display_errors',0);
        }
        return (new Debug())->register();
    }
    /*
     * 展示开发模式下部分数据,内存消耗、耗时、ini设置....
     * */
    static function show()
    {
        return [
            'dump_sql'=>(
                Esapi::instance()->offsetExists('__dbsql_query') ?
                Esapi::instance()['__dbsql_query']:[]
            ),
            'dev'=>array_merge(Esapi::config('dev'),[
                'expend_memory'=>ceil(memory_get_usage ()/1024) .'/kb',
                'allot_merorys'=>ceil(memory_get_usage (true)/1024) .'/kb',
                'expend_time'=>round((microtime (true)-ES_START)*1000,3) .'/ms',
            ]),
            'setting'=>[
                'default_timezone'=>date_default_timezone_get(),
                'enable_opcache' =>opcache_get_status ()?true:false,
                'post_max_size'=>ini_get ('post_max_size'),
                'upload_max_filesize'=>ini_get ('upload_max_filesize'),
                'disable_functions'=>ini_get ('disable_functions')
            ],
            'framework'=>[
                'name'=>Esapi::config('framework.name'),
                'version'=>sprintf('v%s',Esapi::config('framework.version')),
                'author'=>Esapi::config('framework.author'),
                'website'=>Esapi::config('framework.website'),
                'github'=>Esapi::config('framework.github')
            ]
        ];
    }
    /*
     * 注册自定义错误捕捉方法
     * */
    public function register()
    {
        set_exception_handler([$this,static::EXCEPTION_HANDLER]);
        set_error_handler([$this,static::ERROR_HANDLER],E_ALL|E_STRICT);
        register_shutdown_function([$this,static::SHUTDOWN_HANDLER]);
        return Esapi::instance();
    }
}