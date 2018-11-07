<?php

/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

/*
 * 路由配置文件
 * 支持正则路由匹配/[a-z0-9-_]+/[a-zA-Z]/[0-9]
 * 参数模式 : {class} {action} :int :str :any
 * {class} {action} 表示会匹配对应此格式代入配置
 * 条件模式:int 只会匹配数字 [0-9]
 * 条件模式:str 只会匹配字母 [a-zA-Z]
 * 条件模式:any 匹配数字与字母 [a-zA-Z0-9-_]
 * 索引模式 0,1,2 切割path_info 取对应值代入对应配置
 * 正则模式: /[a-z0-9-_]+/[a-zA-Z]/[0-9]
 * [内置参数] class/action/namespace/directory
 * - class 设置访问的class类
 * - action 设置访问的类方法
 * - namespace 设定指定的寻找的命名空间,默认以配置文件为准
 * - directory 设定指定的类目录,例如默认app,设置abc则调用abc目录
 * */
return [
    /*
    * 条件模式 & 参数模式 & 索引模式 & 正则模式
    * */
    '/v1/{class}/:int/:any/[A-Z]+'=>[
        'class'=>'{class}'
    ],
    /*
     * class action 参数模式
     * */
    '/a/{class}'=>[
        'class'=>'{class}'
    ],
    '/a/b/c/{class}/{action}'=>[
        /*
         * 将匹配到的值代入配置
         * */
        'class'=>'{class}',
        'action'=>'{action}'
    ],

    /*
     * 完全匹配模式
     * */
    '/a/b/c'=>[
        'class'=>'b',
        'action'=>'c'
    ],

    /*
     * class action 索引模式2
     * */
    '/aa/bb/cc'=>[
        'class'=>1,
        'action'=>2
    ],


];