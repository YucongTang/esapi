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
 * CURL请求类接口
 * */
interface HttpInterface
{
    /*
     * 发起GET请求
     * @url 请求地址
     * @data 数据内容
     * @options 附带参数 例如TIMEOUT 等
     * */
    public function get($url , $data = [] , $options = []);
    /*
     * 发起POST请求
     * @url 请求地址
     * @data 数据内容
     * @options 附带参数 例如TIMEOUT 等
     * */
    public function post($url , $data = [] , $options = []);
    /*
     * 发起批量GET请求
     * @$urls 数组，请求地址
     * @$options 附带参数
     * @callback 回调函数
     * */
    public function mutiGet($urls , $options = [], $callback = null);
    /*
     * 发起批量POST请求
     * @$urls 数组，请求地址
     * @$options 附带参数
     * @callback 回调函数
     * */
    public function mutiPost($urls , $options = [], $callback = null);
}