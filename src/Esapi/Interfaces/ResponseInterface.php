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
 * 结果响应类处理接口
 * */
interface ResponseInterface
{
    /*
     * 设置状态码
     * */
    public function setStatus( $code );

    /*
     * 设置Header头
     * */
    public function setHeader( $header );

    /*
     * 获取Header头
     * */
    public function getHeader();

    /*
     * 设置响应内容
     * */
    public function setContent( $content );

    /*
     * 获取已设置的内容
     * */
    public function getContent();

    /*
     * 输出结构体
     * */
    public function structure( $error_code , $error_message , $resp_hash , $error_data );

    /*
     * 输出响应
     * */
    public function send();
}