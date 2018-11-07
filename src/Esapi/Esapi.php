<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi;

/*
 * 引入容器
 * */
use Esapi\Console\Event;
use Pimple\Container;

/*
 * 超全局类,Esapi任意地方可以调用
 * */
class Esapi extends Container
{

    /*
     * 定义静态对象存储变量
     * */
    private static $instance;

    /*
     * 静态创建Esapi对象
     * */
    static function instance()
    {
        if(!static::$instance)
        {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /*
     * 全局读取配置文件
     * @param $key 需要读取配置文件的指定key 支持 'app.format'
     * 以.模式调用配置文件数组子层对象
     * @param $file 需要读取的配置文件名 无需.php后缀
     * 调用方式: Esapi::config('app.format');
     * */
    static function config($key , $file = 'config')
    {
        $container_name = implode ('_',['conf',md5($file)]);
        if(!self::instance()->offsetExists($container_name))
        {
            if(!file_exists (ES_APP.__FUNCTION__."/{$file}.php"))
            {
                throw new \Exception(sprintf (
                    "File %s.php does not exist.",$file
                ));
            }
            self::instance()[$container_name] = (require_once ES_APP.__FUNCTION__.sprintf ("/%s.php",$file));
        }
        $conf = self::instance()[$container_name];
        if(empty($key))
        {
            return $conf;
        }
        foreach (explode ('.',$key) as $v)
        {
            if(isset($conf[$v]))
            {
                $conf = $conf[$v];
            }
            else
            {
                $conf = [];
                break;
            }
        }
        return $conf;
    }

    /*
     * HASH生成,生成唯一MD5值
     * @param $digits 可选位数 16 | 32
     * 调用方式 Esapi::hash(32);
     * */
    static function hash( $digits = 16 )
    {
        $hash = md5(implode ('',[
            microtime(),
            uniqid(),
            self::instance()->request->getServer('server_addr')
        ]));
        if($digits == 16)
        {
            $hash = substr($hash,8,16);
        }
        return $hash;
    }

    /*
     * 初始化方法
     * */
    public function initialize($callback , $object)
    {
        foreach (array_merge(array_filter(
            Esapi::config('composer.namespace')),[
            Esapi::config('app.namespace')."\\"=>ES_APP
        ]) as $namespace=>$directory)
        {
            call_user_func_array([$object,'setPsr4'],[
                $namespace,$directory
            ]);
        }
        $files = Esapi::config('composer.files');
        if(!empty($files))
        {
            if(is_string($files))
            {
                require_once ($files);
            }
            elseif(is_array($files))
            {
                foreach (array_filter($files) as $v)
                {
                    require_once "$v";
                }
            }
            throw new \Exception("composer files set error");
        }
        $object->register(true);
        return call_user_func($callback);
    }

    /*
     * 应用运行
     * */
    public function run()
    {
        $match_route_info = $this->route->initialize();
        Event::execute ('ROUTE_AFTER',$match_route_info);
        if(!$match_route_info)
        {
            throw new \Exception(sprintf (
                "Routing match failure [%s]",$this->request->getPathInfo()
            ));
        }
        if(
            !isset($match_route_info['namespace']) ||
            !isset($match_route_info['class']) ||
            !isset($match_route_info['action']) ||
            !isset($match_route_info['path'])
        )
        {
            throw new \Exception(sprintf (
                "Error responding to routing parameters [%s]",$this->request->getPathInfo()
            )) ;
        }
        if(!class_exists ($match_route_info['namespace']))
        {
            throw new \Exception(sprintf (
                "Class %s not found",$match_route_info['namespace']
            ));
        }
        $object = new $match_route_info['namespace'];
        if(!method_exists ($object,$match_route_info['action']))
        {
            throw new \Exception(sprintf (
                "Call to undefined method %s::%s",
                $match_route_info['class'],
                $match_route_info['action']
            ));
        }
        $this->request->setAppNamespace($match_route_info['namespace']);
        $this->request->setAppClass($match_route_info['class']);
        $this->request->setAppAction($match_route_info['action']);
        $this->request->setAppPath($match_route_info['path']);
        Event::execute ('EXECUTE_BEFORE',$match_route_info);
        raise(
            static::config ('response.def_note'),
            static::config ('response.def_code'),
            call_user_func ([$object,$match_route_info['action']])
        );
        return $this;
    }

    /*
     * 魔术方法,动态载入对象
     * */
    public function __get($name)
    {
        $name = strtolower($name);
        if(!in_array($name,self::config('injection')))
        {
            throw new \Exception(sprintf (
                "Service %s not allowed to inject,please set config injection.",$name
            ));
        }
        $category = ['http','input','request','response','route'];
        if(!$this->offsetExists($name))
        {
            $old_class_name = ucfirst($name);
            if(!$class = self::config('reset.'.strtolower($old_class_name)))
            {
                $class = sprintf ("\\Esapi\\%s\\%s",
                    (in_array($name,$category) ? "Http" : "Storage"),
                    $old_class_name
                );
            }
            if(!class_exists($class))
            {
                throw new \Exception(sprintf (
                    "Service %s is not found.",$class
                ));
            }
            $object = new $class;
            $interface = sprintf (
                "\\Esapi\\Interfaces\\%sInterface",$old_class_name
            );
            if(!$object instanceof $interface)
            {
                throw new \Exception(sprintf (
                    "Service %s must inherit %sInterface",$class,$old_class_name
                ));
            }
            $this[$name] = $object;
        }
        return $this[$name];
    }
}