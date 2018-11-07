<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi\Debug\Utils;

use Esapi\Debug\Handle\CliResponse;
use Esapi\Debug\Handle\JsonResponse;
use Esapi\Debug\Handle\XmlResponse;
use Esapi\Esapi;

/*
 * 错误捕捉处理类
 * */
class DebugHandle
{
    /*
     * 待注册的异常捕捉方法名
     * */
    const EXCEPTION_HANDLER = "handleException";
    /*
     * 待注册的Error捕捉方法名
     * */
    const ERROR_HANDLER     = "handleError";
    /*
     * 待注册的错误捕捉方法名error_get_last()
     * */
    const SHUTDOWN_HANDLER  = "handleShutdown";
    /*
     * 错误捕捉响应类Callback方法名
     * */
    const CALLBACK_FUNCTION = 'handle';
    /*
     * 根据框架设置抛出对应错误响应体
     * */
    public function getHandle()
    {
        if(ES_CLI)
        {
            return new CliResponse();
        }
        switch ( strtolower (Esapi::config('app.format')))
        {
            case 'xml':
                $callback = new XmlResponse();
                break;
            default:
                $callback = new JsonResponse();
                break;
        }
        return $callback;
    }
    /*
     * 错误异常捕捉
     * */
    public function handleException($e)
    {
        return call_user_func ([$this->getHandle (),static::CALLBACK_FUNCTION],[
            'message' =>sprintf('Exception: %s',$e->getMessage()),
            'file'=>$e->getFile(),
            'line'=>$e->getLine(),
            'trace'=>$e->getTrace()
        ]);
    }
    /*
     * 全局错误捕捉
     * */
    public function handleError($level, $message, $file = null, $line = null)
    {
        return call_user_func ([$this->getHandle (),static::CALLBACK_FUNCTION],[
            'message'=>sprintf('Error: %s',$message),'file'=>$file,'line'=>$line,'level'=>$level
        ]);
    }
    /*
     * error_get_last()
     * */
    public function handleShutdown()
    {
        $error = error_get_last();

        if($error && $this->isLevelFatal ($error['type']))
        {
            return $this->handleError (
                $error['type'],
                sprintf('Last:%s',$error['message']),
                $error['file'],$error['line']
            );
        }
        
        return false;
    }
    /*
     * 获取错误级别
     * */
    private function isLevelFatal($level)
    {
        $errors = E_ERROR;
        $errors |= E_PARSE;
        $errors |= E_CORE_ERROR;
        $errors |= E_CORE_WARNING;
        $errors |= E_COMPILE_ERROR;
        $errors |= E_COMPILE_WARNING;
        return ($level & $errors) > 0;
    }
}