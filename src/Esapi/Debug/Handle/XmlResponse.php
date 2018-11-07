<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi\Debug\Handle;

use Esapi\Esapi;

/*
 * XML错误响应处理类
 * */
class XmlResponse
{
    public function handle(array $error)
    {
        $error_code = 500;
        $error_data = [];
        $error_note = Esapi::config('dev.error');

        if(ES_DEBUG)
        {
            $error_data = [
                'error'=>$error
            ];
            $error_note = $error['message'];
        }

        raise ($error_note,$error_code,$error_data);
    }
}