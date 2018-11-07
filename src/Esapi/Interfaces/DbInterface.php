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
 * 数据库驱动接口
 * */
interface DbInterface
{
    /*
     * 链接数据库
     * @$server 连接的服务器
     * @persistence 是否长久链接
     * */
    public function connect( $server = 'default' , $persistence = false );

    /*
     * 持久化链接数据库
     * @$server 链接的服务器
     * */
    public function pconnect( $server = 'default' );
}