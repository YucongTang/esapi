<?php

namespace Esapi\Storage;

use Esapi\Console\Event;
use Esapi\Esapi;
use Esapi\Interfaces\DbInterface;
use LessQL\Database;

class Db implements DbInterface
{
    public function connect( $server = 'default' , $persistence = false)
    {
        $container_name = sprintf('dbsql_%s',$server);
        if(Esapi::instance()->offsetExists($container_name))
        {
            return Esapi::instance()[$container_name];
        }
        $dbsql_setting = Esapi::config($server,'dbsql');
        if(empty($dbsql_setting))
        {
            throw new \Exception(sprintf(
                "Db %s setting is not found",$server
            ));
        }
        Esapi::instance()[$container_name] = new Database(
            new \PDO(sprintf(
                "%s:host=%s;port=%s;dbname=%s;charset=%s",
                $dbsql_setting['type'],
                $dbsql_setting['host'],
                $dbsql_setting['port'],
                $dbsql_setting['name'],
                $dbsql_setting['charset']
            ),$dbsql_setting['user'],$dbsql_setting['pass']),
            $persistence?[\PDO::ATTR_PERSISTENT => true]:null
        );
        if($dbsql_setting['prefix'])
        {
            Esapi::instance()[$container_name]->setRewrite( function( $table ) use($dbsql_setting) {

                return sprintf("%s%s",$dbsql_setting['prefix'],$table);

            } );
        }
        Esapi::instance()[$container_name]->setQueryCallback( function( $query, $params ) use ($server) {
            $callback_query = [
                'query'=>$query,
                'params'=>$params,
                'connect'=>$server,
                'sql_type'=>Esapi::config("{$server}.type",'dbsql')
            ];
            if(ES_DEBUG)
            {
                $dbsql_query = '__dbsql_query';
                if(!Esapi::instance()->offsetExists($dbsql_query))
                {
                    Esapi::instance()->offsetSet($dbsql_query,[
                        $callback_query
                    ]);
                }
                else
                {
                    Esapi::instance()[$dbsql_query] = array_merge(Esapi::instance()[$dbsql_query],[
                        $callback_query
                    ]);
                }
            }
            Event::execute('DB_CALLBACK',$callback_query);
        } );
        return Esapi::instance()[$container_name];
    }
    public function pconnect( $server = 'default' )
    {
        return $this->connect($server,true);
    }
    public function __get($name)
    {
        return $this->connect($name);
    }
    public function __call($name, $arguments)
    {
        if($arguments)
        {
            return call_user_func_array([$this->connect(),$name],$arguments);
        }
        return call_user_func([$this->connect(),$name]);
    }
}