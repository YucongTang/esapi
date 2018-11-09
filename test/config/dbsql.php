<?php

return [
    /*
     * 是否启用读写分离
     * */
    'r_w_s'=>false,
    /*
     * 默认配置
     * */
    'default'=>[
        /*
         * 数据库类型,支持mysql|pgsql|sqlite
         * */
        'type'=>'mysql',
        /* host
         * 数据库主机地址
         * */
        'host'  => '101.200.38.217',
        /* name
         * 数据库名
         * */
        'name'  => 'base',
        /* user
         * 数据库用户名
         * */
        'user'  => 'root',
        /* pass
         * 数据库密码
         * */
        'pass'  => 'Zy@test#mysql',
        /* port
         * 数据库端口
         * */
        'port'  => 3306,
        /* coding
         * 数据库编码
         * */
        'charset'=> 'utf8mb4',
        /* prefix
         * 数据库数据表前缀
         * */
        'prefix'=> '',
    ]
];