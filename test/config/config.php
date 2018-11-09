<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

return [

    /*
     * 开发模式配置
     * */
    'dev'=>[
        /*
         * 是否启用DEBUG,如果设为true将抛出详细错误,若设为false，shows配置将无效
         * */
        'debug'=>true,
        /*
         * 当Debug为false,屏蔽错误并显示此信息
         * */
        'error'=>'Internal error.',
        /*
         * 测试环境IP地址,请注意是内网IP[此项是为了防止配置文件误上生产环境而设立]
         * 若检测到本机IP为设置项,则debug可用,若非设置ip则debug强制设为关闭状态
         * 默认为空,空代表不验证,支持数组
         * */
        'ip'=>'',
        /*
         * 当IP已设置且当前服务器IP为已设置的IP
         * IP未设置则此项不会生效,若已设置
         * 系统自动调用如下配置文件
         * */
        'test'=>[
//            'dbsql'=>'dbsql_test',
//            'nosql'=>'nosql_test',
//            'routes'=>'routes_test'
        ]
    ],

    /*
     * 配置文件路径重试,设置后对应配置文件将从此处加载
     * */
    'config_set_path'=>[
//        'database'=>'/tmp/',
    ],

    /*
     * 应用基本配置
     * */
    'app'=>[
        /*
         * 应用输出格式，默认json 支持 json/xml
         * */
        'format'=>'json',
        /*
         * 唯一请求hash生成位数,[16|32]16位或32位
         * */
        'hash_digits'=>16,
        /*
         * 应用根命名空间，例如 namespace test;
         * */
        'namespace'=>'test',
        /*
         * 应用控制器存放目录
         * */
        'directory'=>'app',
        /*
         * 请求地址后缀
         * */
        'link_suffix'=>'',
        /*
         * XSS过滤
         * */
        'xss_filter'=>false,
    ],

    /*
     * 路由缓存,高并发建议开启,减少路由性能消耗
     * */
    'route_cache'=>[
        /*
         * 是否启用路由缓存
         * */
        'enable'=>false,
        /*
         * 使用什么方式进行路由缓存，默认redis 寻找位置 nosql.php redis > default
         * */
        'use_set'=>'redis.default',
        /*
         * 缓存时长，单位秒
         * */
        'expires_in'=>86400
    ],

    /*
     * 基础默认配置信息
     * */
    'default'=>[
        /*
         * 默认入口文件名
         * */
        'index'=>'index.php',
        /*
         * 默认首页控制器
         * */
        'home_class'=>'index',
        /*
         * 默认首页控制器方法
         * */
        'home_action'=>'index',
        /*
         * 当路由未匹配到控制器时触发notFound，此时将会调起此类
         * */
        '404_class'=>'_empty',
        /*
         * 默认notFound控制器方法
         * */
        '404_action'=>'index',
        /*
         * 应用运行默认时区
         * */
        'time_zone'=>'Asia/Shanghai'
    ],

    /*
     * 超全局钩子-reset支持你重构底层服务
     * 支持重构的服务['request','response','route','input','http','cache','db','file','cookie','memcache','redis','session']
     * 示范[重构input]: "input"=>"\\test\\Reset\\Input"
     * 或者: "input"=>"\\test\\Hook\\Input"
     * 所重构的服务必须继承对应接口,例如继承input接口
     * class Input implements InputInterface{}
     * */
    "reset"=>[
        //"input"=>"\\test\\Reset\\Input"
    ],

    /*
     * 服务注入,在此处注入过的服务调用方法
     * Esapi::instance()->request 若在控制器中则 $this->request
     * request,response,route 必须注入,否则应用将瘫痪
     * 若未注入,将无法使用对应服务
     * */
    'injection'=>[
        'request','response','route','input','http','db','redis'
    ],

    /*
     * composer自动加载
     * 若你想跨项目调用,示范: "test1\\"=>"/www/test1" [KEY（命名空间）=>VALUE（项目目录）]
     * files 则是自动载入文件，例如你想增加全局 function方法，可用此加载
     * 示范：["/www/test/func.php"]
     * */
    "composer"=>[
        /*
         * 标准的psr4规范,若不熟悉请百度
         * */
        "psr4"=>[
        ],
        /*
         * 需要载入的全局文件，此数组是一维数组
         * */
        "files"=>[
        ]
    ],

    /*
     * 应用响应配置
     * */
    'response'=>[
        /*
         * 默认输出message/note {code:0,note:successful}
         * */
        'def_note'=>'successful',
        /*
         * 默认输出code {code:0}
         * */
        'def_code'=>0,
        /*
         * 输出结构体列如 [code=>0,note=>success,resp=>xxx,data=>[]]
         * */
        'structure'=>[
            'response_code_name'=>'code',
            'response_note_name'=>'note',
            'response_hash_name'=>'resp',
            'response_data_name'=>'data'
        ]
    ],

    /*
     * 框架信息,请尊重开发者保留此项
     * */
    'framework'=>[
        'name'=>'esapi restful',
        'version'=>'1.0',
        'author'=>'huakaiquan@qq.com',
        'website'=>'https://esapi.wss.me',
        'github'=>'https://github.com/wss-dev/esapi'
    ],

    /*
     * 中间件配置
     * 执行优先级由在数组中的排序为准
     * 编写中间件必须继承接口 EventInterface
     * */
    'event'=>[
        /*
         * 路由介入之前执行下列中间件
         * */
        'ROUTE_BEFORE'=>[
            /*
             * [KEY(命名空间)=>VALUE(当中间件无输出或无终止且返回false默认输出此value)]
             * */
//            "\\test\\event\\BlockCheck"=>"You have been blacklisted"
        ],
        /*
         * 路由匹配到信息之后执行,此刻还未验证路由的有效性 传递参数[$match_route_info] 路由匹配信息
         * */
        'ROUTE_AFTER'=>[],
        /*
         * 应用控制器执行之前执行 传递参数[$match_route_info] 路由匹配信息
         * */
        'EXECUTE_BEFORE'=>[],
        /*
         * 应用控制器执行之后执行 传递参数[$callback_content] 控制器执行返回数据（包括触发Error的错误信息）
         * */
        'EXECUTE_AFTER'=>[],
        /*
         * 数据库执行之前CALLBACK 传递参数[$query,$connect,$params,$type]
         * */
        'DB_CALLBACK'=>[],
        /*
         * 缓存执行之前触发CALLBACK 传递参数[func(执行的缓存方法名),arg1,arg2,arg^n...]
         * */
        'CACHE_CALLBACK'=>[]
    ]
];