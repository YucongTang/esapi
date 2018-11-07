<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi\Http;

use Esapi\Esapi;
use Esapi\Interfaces\RouteInterface;

/*
 * 路由处理类
 * */
class Route implements RouteInterface
{
    /*
     * :int 正则
     * */
    const MATCH_INT = '[0-9]+';
    /*
     * :str 正则
     * */
    const MATCH_STR = '[a-zA-Z]+';
    /*
     * :any 正则
     * */
    const MATCH_ANY = '[0-9a-zA-Z-_]+';
    /*
     * 默认配置存放
     * */
    private $default=[];
    /*
     * 当前web link
     * */
    private $uri;
    /*
     * 当前类文件存放地址
     * */
    private $file_path;

    /*
     * 魔术方法
     * */
    public function __construct()
    {
        $this->default = [
            'home'=>sprintf("/%s/%s",
                Esapi::config('default.home_class'),
                Esapi::config('default.home_action')
            ),
            '404'=>sprintf("/%s/%s",
                Esapi::config('default.404_class'),
                Esapi::config('default.404_action')
            )
        ];
        $this->uri = Esapi::instance()->request->getPathInfo();
        $this->file_path = sprintf("%s%s",ES_APP,
            Esapi::config('app.directory')
        );
    }

    /*
     * 初始化匹配
     * */
    public function initialize ()
    {
        if(Esapi::config ('route_cache.enable'))
        {
            if( $match_route_info = Esapi::instance ()->cache->connect(
                Esapi::config ('route_cache.use_set')
            )->get(sprintf ('route_cache_%s',md5 ($this->uri))))
            {
                return $match_route_info;
            }
        }
        if(in_array(Esapi::instance()->request->getPathInfo(),$this->default))
        {
            $match_route_info = $this->matchStringRoute();
        }
        else
        {
            $match_route_info = $this->matchNormalRoute();
        }
        if($match_route_info === false || !class_exists($match_route_info['namespace']))
        {
            if(ES_DEBUG === false)
            {
                $this->uri = $this->default['404'];
            }
            return $this->matchStringRoute();
        }
        if(Esapi::config('route_cache.enable'))
        {
            Esapi::instance()->cache->connect()->set(
                sprintf ('route_cache_%s',md5 ($this->uri)),
                $match_route_info,Esapi::config('route_cache.expires_in')
            );
        }
        return $match_route_info;
    }

    /*
     * 字符串匹配
     * */
    private function matchStringRoute()
    {
        $uri_array = explode("/", trim($this->uri, '/'));
        $uri_array_count = count($uri_array);
        $match_info = [];
        switch ($uri_array_count)
        {
            case 1:
                $match_info = [
                    'class'=>$uri_array[0],
                    'action'=>Esapi::config('default.home_action'),
                    'path'=>''
                ];
                break;
            default:
                $last_file = sprintf("%s.php",$this->file_path.$this->uri);
                if(!is_file($last_file))
                {
                    $match_info['class'] = array_slice($uri_array,-2,1)[0];
                    $match_info['action']= array_slice($uri_array,-1,1)[0];
                }
                else
                {
                    $match_info['class'] = array_slice($uri_array,-1,1)[0];
                    $match_info['action']= Esapi::config('default.home_action');
                }
                $length = array_search($match_info['class'],$uri_array);
                $match_info['path'] = '';
                if($length > 0)
                {
                    $match_info['path'] .= implode("\\",array_slice($uri_array,0,$length));
                }
                break;
        }
        $match_info['namespace'] = implode("\\",array_filter(
            [
                Esapi::config('app.namespace'),
                Esapi::config('app.directory'),
                $match_info['path'],$match_info['class']
            ]
        ));
        return $match_info;
    }

    /*
     * 正则匹配
     * */
    private function matchNormalRoute()
    {
        $routes = array_filter(Esapi::config ('','routes'));
        if(empty($routes))
        {
            return $this->matchStringRoute();
        }
        foreach ($routes as $match=>$values)
        {
            $is_set_action = true;
            if(!isset($values['class']))
            {
                return false;
            }
            if(!isset($values['action']))
            {
                $is_set_action = false;
                $values['action'] = Esapi::config('default.home_action');
            }
            $match_preg = str_replace([
                '{class}','{action}',':int',':str',':any'
            ],[
                static::MATCH_ANY,static::MATCH_ANY,static::MATCH_INT,static::MATCH_STR,static::MATCH_ANY
            ],$match);

            if(preg_match(sprintf('#^%s$#',$match_preg),$this->uri))
            {
                $match_array = explode('/',trim($match,'/'));
                $link_array  = explode('/',trim($this->uri,'/'));
                $namespace   = Esapi::config('app.namespace');
                $directory   = Esapi::config('app.directory');
                $route_info  = array_merge([],
                    $this->checkParams($link_array,$match_array,$values),
                    $this->checkParams($link_array,$match_array,$values,'action')
                );
                $route_info['path'] = trim(strstr($this->uri,$route_info['class'],true),'/');
                if(isset($values['namespace']))
                {
                    $namespace = $values['namespace'];
                }
                if(isset($values['directory']))
                {
                    $directory = $values['directory'];
                }
                $end_location_index = array_search(
                    $is_set_action?$route_info['action']:$route_info['class'],
                    $link_array)+1;
                if($end_location_index < count($link_array))
                {
                    if(!isset($values['method']) || strtolower($values['method']) != 'post')
                    {
                        $values['method'] = 'get';
                    }
                    call_user_func(
                        [Esapi::instance()->request,implode('',['set',ucfirst(strtolower($values['method']))])],
                        array_slice($link_array,$end_location_index)
                    );
                }
                return array_merge($route_info,[
                    'namespace'=>implode("\\",array_filter(
                        [
                            $namespace,$directory,
                            str_replace('/',"\\",$route_info['path']),
                            $route_info['class']
                        ]
                    ))
                ]);
            }
        }
        return false;
    }

    /*
     * 核验参数
     * */
    private function checkParams($link_array,$match_array,$values,$type = 'class')
    {
        if($values[$type] === "{{$type}}")
        {
            $location_index = array_search("{class}",$match_array);
            $match_info = [
                $type=>array_slice($link_array,$location_index,1)[0]
            ];
        }elseif(is_int($values[$type]) && $values[$type] <= count($link_array))
        {
            $match_info = [
                $type=>array_slice($link_array,$values[$type],1)[0]
            ];
        }elseif(is_string($values[$type]))
        {
            $match_info = [
                $type=>$values[$type]
            ];
        }
        return $match_info;
    }
}