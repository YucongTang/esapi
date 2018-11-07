<?php

namespace Esapi\Storage;

use Esapi\Console\Event;
use Esapi\Esapi;
use Esapi\Interfaces\RedisInterface;

class Redis implements RedisInterface
{
    private static $lastServer = null;

    public function connect( $server = 'default' ,  $persistence = false )
    {
        static::$lastServer = $server;
        $container_name = sprintf('nosql_%s',$server);
        if(Esapi::instance()->offsetExists($container_name))
        {
            return Esapi::instance()[$container_name];
        }
        $nosql_setting = Esapi::config("redis.{$server}",'nosql');
        if(empty($nosql_setting))
        {
            throw new \Exception(sprintf(
                "Nosql %s setting is not found",$server
            ));
        }
        $redis = new \Redis();
        if($persistence)
        {
            $redis->pconnect(
                $nosql_setting['host'],$nosql_setting['port'],$nosql_setting['timeout']
            );
        }
        else
        {
            $redis->connect(
                $nosql_setting['host'],$nosql_setting['port'],$nosql_setting['timeout']
            );
        }
        if(!empty($nosql_setting['auth']))
        {
            $redis->auth($nosql_setting['auth']);
        }
        Esapi::instance()["nosql_{$server}"] = $redis;
        return $this;
    }

    public function pconnect( $server = 'default' )
    {
        $this->connect( $server , true);
    }

    public function __call($name, $arguments)
    {
        if(static::$lastServer  === null) $this->connect();
        Event::execute('CACHE_CALLBACK',[
            'func'=>$name,
            'args'=>$arguments,
            'server'=>static::$lastServer,
            'type'=>'redis'
        ]);
        if(array_filter($arguments))
        {
            foreach ($arguments as $k=>$v)
            {
                if(is_array($v) || is_object($v))
                {
                    $arguments[$k] = json_encode($v);
                }
            }
            $result = call_user_func_array([Esapi::instance()['nosql_'.static::$lastServer],$name],$arguments);
        }
        else
        {
            $result = call_user_func([Esapi::instance()['nosql_'.static::$lastServer],$name]);
        }
        if(is_string($result))
        {
            return json_decode($result,true);
        }

        return $result;
    }

}