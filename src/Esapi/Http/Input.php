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
use Esapi\Interfaces\InputInterface;

/*
 * 输入处理类
 * */
class Input implements InputInterface
{
    /*
     * Method 类型判断
     * */
    private $isMethod = [
        'isGet'=>'get','isPost'=>'post','isPut'=>'put','isDelete'=>'delete','isOption'=>'option'
    ];

    /*
     * Ajax Method 类型判断
     * */
    private $isAjaxMethod = [
        'isAjaxGet'=>'get','isAjaxPost'=>'post','isAjaxPut'=>'put','isAjaxDelete'=>'delete','isAjaxOption'=>'option'
    ];

    /*
     * 魔术方法
     * */
    public function __call($name, $arguments)
    {
        if(in_array($name,array_keys($this->isMethod)))
        {
            return ($this->method() === $this->isMethod[$name] ? true:false);
        }elseif(in_array($name,array_keys($this->isAjaxMethod)))
        {
            return (($this->isAjax() && $this->method() === $this->isAjaxMethod[$name]) ? true:false);
        }
        throw new \Exception(sprintf(
            "No corresponding method was found[%s::%s]",__CLASS__,$name
        ));
    }

    /*
     * 获取$_GET输入值
     * @$key 需要获取的指定键名
     * @$default 设置默认值
     * @xss 是否xss过滤 默认开启
     * */
    public function get($key = '' , $default = null , $xss = true)
    {
        return $this->deal($key , $default , $_GET , $xss);
    }

    /*
     * 获取$_POST输入值
     * @$key 需要获取的指定键名
     * @$default 设置默认值
     * @xss 是否xss过滤 默认开启
     * */
    public function post($key = '' , $default = null , $xss = true)
    {
        $input = [];
        parse_str( file_get_contents("php://input" , $input));
        return $this->deal( $key , $default , array_merge($input ,$_POST) , $xss);
    }

    /*
     * 获取$_GET或$_POST输入值
     * @$key 需要获取的指定键名
     * @$default 设置默认值
     * @xss 是否xss过滤 默认开启
     * */
    public function any($key = '' , $default = null , $xss = true)
    {
        return $this->deal($key , $default , array_merge(
            $this->get('',null,false),
            $this->post('',null,false)
        ) , $xss);
    }

    /*
     * 判断是否为ajax
     * */
    public function isAjax()
    {
        if( Esapi::instance()->request->getHeader('X_REQUESTED_WITH') === "xmlhttprequest" )
        {
            return true;
        }
        return false;
    }

    /*
     * 获取请求Method模式
     * */
    public function method()
    {
        return strtolower(Esapi::instance()->request->getServer('REQUEST_METHOD'));
    }

    /*
     * 获取真实的请求IP
     * */
    public function ip($proxy = true)
    {
        if ($proxy) {
            $ip = Esapi::instance()->request->getHeader('X_FORWARDED_FOR');
        } else {
            $ip = Esapi::instance()->request->getHeader('CLIENT_IP');
        }
        if (empty($ip)) {
            $ip = Esapi::instance()->request->getServer('REMOVE_ADDR');
        }
        if ($p = strrpos($ip, ",")) {
            $ip = substr($ip, $p+1);
        }
        return trim($ip);
    }

    /*
     * 判断referer
     * */
    public function referrer($restrict = true, $allow = '')
    {
        $referrer = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : null;
        if (empty($referrer)) { return true;    } /* 空的 referer 直接允许 */
        if ($restrict) {
            $url = parse_url($referrer);
            if (empty($url['host'])) { return false; }
            $allow = '/'.str_replace('.', '\.', $allow).'/';
            return 0 < preg_match($allow, $url['host']);
        }
        return false !== strpos($referrer, $allow);
    }

    /*
     * 私有方法处理
     * */
    private function deal( $key , $default , $data , $xss )
    {
        if(empty($data))
        {
            return $default;
        }
        elseif(empty($key))
        {
            return $this->xss($data , $xss);
        }
        elseif(isset($data[$key]))
        {
            $value = $data[$key];
            if(empty($value) && $default !== null)
            {
                $value = $default;
            }
            return $this->xss($value , $xss);
        }
        return $default;
    }

    /*
     * 私有方法XSS过滤
     * */
    private function xss( $data = null , $xss)
    {
        if(Esapi::config('app.xss_filter') === false || $xss === false)
        {
            return $data;
        }
        if(is_string($data))
        {
            return strip_tags(htmlentities($data));
        }
        elseif(is_array($data))
        {
            foreach ($data as $k=>$v)
            {
                $data[$k] = strip_tags(htmlentities($v));
            }
        }
        return $data;
    }
}