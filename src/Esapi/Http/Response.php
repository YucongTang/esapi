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
use Esapi\Interfaces\ResponseInterface;

/*
 * 结果响应处理类
 * */
class Response implements ResponseInterface
{
    /*
     * CGI模式header头状态码
     * */
    private $status_array = [
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out"
    ];

    /*
     * header头信息
     * */
    private $header = null;

    /*
     * 输出内容
     * */
    private $content = null;

    /*
     * 设置header状态码code
     * @$code 状态码
     * */
    public function setStatus($code)
    {
        if(isset($this->status_array[$code?:200]))
        {
            return $this->setHeader($this->status_array[$code?:200]);
        }

        return true;
    }

    /*
     * 设置header头
     * @$header 数组
     * */
    public function setHeader($header)
    {
        if(is_string($header))
        {
            $this->header.= $header."\r\n";
        }

        if(is_array($header))
        {
            foreach ($header as $v) $this->header .= $v."\r\n";
        }

        return true;
    }

    /*
     * 获取已设置的header头信息
     * */
    public function getHeader()
    {
        return $this->header;
    }

    /*
     * 设置输出内容
     * @$content 内容string
     * */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /*
     * 获取已设置的输出内容
     * */
    public function getContent()
    {
        return $this->content;
    }

    /*
     * 格式化输出结构体
     * @$error_code 状态码
     * @$error_message 信息
     * @$resp_hash 唯一请求值
     * @$error_data 内容
     * */
    public function structure($error_code , $error_message , $resp_hash , $error_data)
    {
        $defaultStructure = array_merge([
            'response_code_name'=>'code',
            'response_note_name'=>'note',
            'response_hash_name'=>'resp',
            'response_data_name'=>'data'
        ],Esapi::config('response.structure'));

        return [
            $defaultStructure['response_code_name'] => $error_code,
            $defaultStructure['response_note_name'] => $error_message,
            $defaultStructure['response_hash_name'] => $resp_hash,
            $defaultStructure['response_data_name'] => $error_data
        ];
    }

    /*
     * 输出内容
     * @$async 是否异步输出
     * */
    public function send( $async = false )
    {
        if(ES_CLI)
        {
            echo $this->getContent();
            exit;
        }
        header($this->getHeader());
        echo $this->getContent();
        if($async === false)
        {
            exit;
        }
        fastcgi_finish_request ();
    }
}