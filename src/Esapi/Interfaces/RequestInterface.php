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
 * web请求处理类接口
 * */
interface RequestInterface
{
    /*
     * 获取请求地址的PathInfo
     * */
    public function getPathInfo();
    /*
     * 获取请求非HEADER头段
     * */
    public function getServer( $server_name );
    /*
     * 获取请求HEADER头段
     * */
    public function getHeader( $header_name );
    /*
     * 获取路由解析完毕Class
     * */
    public function getAppClass();
    /*
     * 获取路由解析完毕Action
     * */
    public function getAppAction();
    /*
     * 获取路由解析完毕Namespace
     * */
    public function getAppNamespace();
    /*
     * 获取路由解析完毕Path
     * */
    public function getAppPath();
    /*
     * 设置解析Class
     * */
    public function setAppClass( $class );
    /*
     * 设置解析Action
     * */
    public function setAppAction( $action );
    /*
     * 设置解析Namespace
     * */
    public function setAppNamespace( $namespace );
    /*
     * 设置解析Path
     * */
    public function setAppPath( $path );
    /*
     * 设置$_GET值
     * */
    public function setGet( $args );
    /*
     * 设置$_POST值
     * */
    public function setPost( $args );
}