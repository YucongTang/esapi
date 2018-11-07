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
 * CLI模式处理类
 * */
class CliResponse
{
    public function handle(array $error)
    {
        $error_code = 500;
        $error_note = Esapi::config('dev.error');
        $content  = sprintf("\n[DEBUG][%s]:%s",
            Esapi::config('response.structure.response_code_name'),$error_code
        );
        $content .= sprintf("\n[DEBUG][%s]:%s",
            Esapi::config('response.structure.response_note_name'),$error_note
        );
        $content .= sprintf("\n[DEBUG][%s]:%s",
            Esapi::config('response.structure.response_hash_name'),ES_HASH
        );
        if(ES_DEBUG)
        {
            $content .= sprintf("\n[DEBUG][%s]{ -------------------------------- }",
                Esapi::config('response.structure.response_data_name')
            );
            foreach ($error as $k=>$v)
            {
                $content .= sprintf(
                    "\n[DEBUG][%s]->>>|-[%s]:%s",
                    Esapi::config('response.structure.response_data_name'),
                    $k,is_array($v) ? json_encode($v):$v
                );
            }
            $content .= sprintf("\n[DEBUG][%s]{ -------------------------------- }",
                Esapi::config('response.structure.response_data_name')
            );
        }
        $content .= "\n";
        Esapi::instance()->response->setContent($content)->send();
    }
}