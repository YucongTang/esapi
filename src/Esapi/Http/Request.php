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
use Esapi\Interfaces\RequestInterface;

/*
 * 请求处理类[支持自定义]
 * */
class Request implements RequestInterface
{
    /*
     * 路由处理完毕得到应用命名空间
     * */
    private $appNamespace = null;
    /*
     * 路由处理完毕得到应用Class
     * */
    private $appClass     = null;
    /*
     * 路由处理完毕得到应用Action
     * */
    private $appAction    = null;
    /*
     * 路由处理完毕得到应用Path
     * */
    private $appPath      = null;
    /*
     * 获取请求的PATH_INFO信息
     * */
    public function getPathInfo()
    {
        if(!$http_request_url = $this->getServer('request_uri'))
        {
            $http_request_url = $this->getServer('document_uri');
        }
        $http_request_url = sprintf("/%s",
            trim(str_replace(
                "/".Esapi::config('default.index'),'',$http_request_url
            ),'/')
        );
        $http_request_url = explode('?',$http_request_url)[0];
        if(Esapi::config('app.link_suffix') && $http_request_url !== '/')
        {
            if(!strpos($http_request_url,Esapi::config('app.link_suffix')))
            {
                return sprintf("/%s/%s",
                    Esapi::config('default.404_class'),
                    Esapi::config('default.404_action')
                );
            }
            else
            {
                $http_request_url = str_replace(
                    Esapi::config('app.link_suffix'),'',$http_request_url
                );
            }
        }
        if($http_request_url === sprintf('/%s/%s',
                Esapi::config('default.home_class'),
                Esapi::config('default.home_action')
            ) ||
            $http_request_url === sprintf('/%s',Esapi::config('default.home_class')
            ) ||
            $http_request_url === '/'
        )
        {
            return sprintf('/%s/%s',
                Esapi::config('default.home_class'),
                Esapi::config('default.home_action')
            );
        }
        return $http_request_url;
    }
    /*
     * 获取请求非HEADER头段
     * */
    public function getServer( $server_name = null )
    {
        if( $server_name === null )
        {
            $result = [];
            foreach ($_SERVER as $server_key => $server_value)
            {
                if(substr($server_key,0,5) == 'HTTP_') continue;
                array_push($result,[$server_key=>$server_value]);
            }
            return $result;
        }
        $server_name = strtoupper($server_name);
        if( isset($_SERVER[$server_name]))
        {
            return $_SERVER[$server_name];
        }
        return false;
    }
    /*
     * 获取请求HEADER头段
     * */
    public function getHeader( $header_name )
    {
        if( $header_name === null )
        {
            $result = [];
            foreach ($_SERVER as $header_key => $header_value)
            {
                if(substr($header_key,0,5) != 'HTTP_') continue;
                array_push($result,[$header_key=>$header_value]);
            }
            return $result;
        }
        $header_name = strtoupper("HTTP_$header_name");
        if( isset($_SERVER[$header_name]))
        {
            return $_SERVER[$header_name];
        }
        return false;
    }
    /*
     * 获取路由解析完毕Class
     * */
    public function getAppClass()
    {
        return $this->appClass;
    }
    /*
     * 获取路由解析完毕Action
     * */
    public function getAppAction()
    {
        return $this->appAction;
    }
    /*
     * 获取路由解析完毕Namespace
     * */
    public function getAppNamespace()
    {
        return $this->appNamespace;
    }
    /*
     * 获取路由解析完毕Path
     * */
    public function getAppPath()
    {
        return $this->appPath;
    }
    /*
     * 设置解析Class
     * */
    public function setAppClass( $class )
    {
        $this->appClass = $class;
        return $this;
    }
    /*
     * 设置解析Action
     * */
    public function setAppAction( $action )
    {
        $this->appAction = $action;
        return $this;
    }
    /*
     * 设置解析Namespace
     * */
    public function setAppNamespace( $namespace )
    {
        $this->appNamespace = $namespace;
        return $this;
    }
    /*
     * 设置解析Path
     * */
    public function setAppPath( $path )
    {
        $this->appPath = $path;
        return $this;
    }
    /*
     * 设置$_GET值
     * */
    public function setGet( $args )
    {
        if(!isset($_GET['args']))
        {
            $_GET['args'] = [];
        }
        if(is_array($args))
        {
            $_GET['args'] = array_merge($_GET['args'],$args);
        }
        else
        {
            array_push($_GET['args'],$args);
        }
    }
    /*
     * 设置$_POST值
     * */
    public function setPost( $args )
    {
        if(!isset($_POST['args']))
        {
            $_POST['args'] = [];
        }
        if(is_array($args))
        {
            $_POST['args'] = array_merge($_POST['args'],$args);
        }
        else
        {
            array_push($_POST['args'],$args);
        }
    }
}