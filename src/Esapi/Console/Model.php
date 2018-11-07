<?php
/* -----------------------------------------
 * 极致轻量API框架,最快的上手模式定义开发
 * 全中文注释框架,支持Composer扩展
 * Github: https://github.com/wss-dev/esapi
 * 社区: https://esapi.wss.me
 * 遵循开源协议,你可以进行二次开发,但请保留版权
 *-----------------------------------------*/

namespace Esapi\Console;

use Esapi\Esapi;

/*
 * 基础模型封装类
 * */
class Model
{
    /*
     * 私有方法链接数据库
     * */
    private static function db( $ac = 'r' )
    {
        if(Esapi::config('r_w_s','dbsql') === true)
        {
            $db = Esapi::instance()->db->{$ac};
        }
        else
        {
            $db = Esapi::instance()->db;
        }
        return $db;
    }

    /*
     * 私有方法获取当前执行的数据表
     * */
    private static function getExecuteTable()
    {
        if(is_callable(sprintf("%s::table",get_called_class())))
        {
            return call_user_func(sprintf("%s::table",get_called_class()));
        }
        return basename(str_replace("\\","/",get_called_class()));
    }

    /*
     * 私有方法选定数据表
     * */
    private static function autoSelectTable($db)
    {
        return call_user_func([$db,self::getExecuteTable()]);
    }

    /*
     * 私有方法处理条件筛选
     * */
    private static function deal($ac ,array $options = [])
    {
        $db = self::autoSelectTable(self::db($ac));
        if(isset($options['where']) && !empty($options['where']))
        {
            $db = $db->where($options['where']);
        }
        if(isset($options['select']) && !empty($options['select']))
        {
            $db = $db->select($options['select']);
        }
        if(isset($options['orderBy']) && !empty(array_filter($options['orderBy'])))
        {
            foreach ($options['orderBy'] as $field=>$sort)
            {
                $db = $db->orderBy($field,$sort);
            }
        }
        if(isset($options['limit']) && !empty($options['limit']))
        {
            if (is_int($options['limit']))
            {
                $db = $db->limit($options['limit']);
            }
            elseif(is_array($options['limit']))
            {
                $db = $db->limit($options['limit'][0],$options['limit'][1]);
            }
        }
        if(isset($options['whereNot']) && !empty($options['whereNot']))
        {
            $db = $db->whereNot($options['whereNot']);
        }
        return $db;
    }

    /*
     * 私有方法处理字段值增加或减少
     * */
    private static function inOrDe(array $update ,array $where = [] ,$in = true)
    {
        if(empty($update))
        {
            return false;
        }
        $update_set = [];
        $where_set  = [];
        $where_str  = '';
        foreach ($update as $f=>$n)
        {
            array_push($update_set,sprintf(
                "`%s`=%s%s%s",$f,$f,($in?'+':'-'),intval($n)
            ));
        }
        if(!empty($where))
        {
            foreach ($where as $f=>$v)
            {
                array_push($where_set,sprintf(
                    "%s'%s'",strstr($f,' ')?"$f":"`$f` = ",trim($v)
                ));
            }

            $where_str .= sprintf(" where %s",trim(implode(' and ',$where_set)));
        }

        return sprintf(
            "update %s set %s %s",
            self::getExecuteTable(),
            implode(',',$update_set),
            $where_str
        );
    }

    /*
     * 事物处理begin
     * 开始事物
     * */
    static function begin()
    {
        return self::db('w')->begin();
    }

    /*
     * 事物处理rollback
     * 回滚事物
     * */
    static function rollback()
    {
        return self::db('w')->rollback();
    }

    /*
     * 事物处理commit
     * 提交事物
     * */
    static function commit()
    {
        return self::db('w')->commit();
    }

    /*
     * 取数据单条
     * @$where 筛选条件
     * @$options 更多筛选条件,具体请看官方文档
     * */
    static function fetch(array $where = [] ,array $options = [])
    {
        return object2array(call_user_func([
            self::deal(
                'r', array_merge($options,['limit'=>1,'where'=>$where])
            ),__FUNCTION__
        ]));
    }

    /*
     * 取数据多条
     * @$where 筛选条件
     * @$options 更多筛选条件,具体请看官方文档
     * */
    static function fetchAll(array $where = [] ,array $options = [])
    {
        return object2array(
            call_user_func([
                self::deal(
                    'r', array_merge($options,['where'=>$where])
                ),__FUNCTION__
            ])
        );
    }

    /*
     * 数据计数
     * @$where 筛选条件
     * @$options 更多筛选条件,具体请看官方文档
     * */
    static function count(array $where = [] ,array $options = [])
    {
        return call_user_func([
                self::deal(
                    'r', array_merge($options,['where'=>$where])
                ),__FUNCTION__
            ]);
    }

    /*
     * 单条插入
     * @insert 需要插入的键值对应数组(field=>value)
     * @insert_id 是否返回insert id
     * */
    static function insert(array $insert = [] , $insert_id = false)
    {
        if(empty($insert))
        {
            return false;
        }
        $res = self::db('w')->insert($insert);
        return (
            $insert_id ? $res->rowCount() : self::db('w')->lastInsertId()
        );
    }

    /*
     * 批量插入多维数组
     * @inserts 需要插入的键值对应数组[[field=>value],[field=>value]]
     * */
    static function batchInsert(array $inserts = [])
    {
        if(empty($inserts))
        {
            return false;
        }
        return self::db('w')->insert($inserts,'batch')->rowCount();
    }

    /*
     * 获取最后一次SQL执行的Insert ID
     * */
    static function insertId()
    {
        return self::db('w')->lastInsertId();
    }

    /*
     * 更新数据列
     * @$where 筛选条件
     * @$update 更新数据[field=>value,field2=>value2]
     * @$options 更多筛选条件,具体请看官方文档
     * */
    static function update(array $where = [] , array $update = [] ,array $options = [])
    {
        if(empty($update))
        {
            return false;
        }
        return self::deal('w',array_merge($options,['where'=>$where]))->update($update)->rowCount();
    }

    /*
     * 删除数据
     * @$where 筛选条件
     * @$options 更多筛选条件,具体请看官方文档
     * */
    static function delete(array $where= [] , array $options = [])
    {
        return self::deal('w',array_merge($options,['where'=>$where]))->delete()->rowCount();
    }

    /*
     * 取字段最小值
     * @$expr 字段
     * @$options 更多筛选条件,具体请看官方文档
     * */
    static function min($expr , array $options = [])
    {
        return self::deal('r',$options)->min($expr);
    }

    /*
     * 取字段最大值
     * @$expr 字段
     * @$options 更多筛选条件,具体请看官方文档
     * */
    static function max($expr , array $options = [])
    {
        return self::deal('r',$options)->max($expr);
    }

    /*
    * 取字段和值
    * @$expr 字段
    * @$options 更多筛选条件,具体请看官方文档
    * */
    static function sum($expr , array $options = [])
    {
        return self::deal('r',$options)->sum($expr);
    }

    /*
     * 指定值进行递增更新
     * @$update 需要更新的字段 [field=>value,field2=>value2,num=>1,stock=>2]
     * @$where 筛选条件
     * */
    static function incr(array $update ,array $where = [])
    {
        return self::query(self::inOrDe($update,$where))->rowCount();
    }

    /*
     * 指定值进行递减更新
     * @$update 需要更新的字段 [field=>value,field2=>value2,num=>1,stock=>2]
     * @$where 筛选条件
     * */
    static function decr(array $update ,array $where = [])
    {
        return self::query(self::inOrDe($update,$where,false))->rowCount();
    }

    /*
     * 原生SQL语句执行
     * @$sql sql语句
     * */
    static function query($sql)
    {
        $result = call_user_func([
            self::db(
                in_array(strtolower(substr($sql,0,6)),[
                    'insert','update','delete','create'
                ]) ? 'w':'r'
            ),__FUNCTION__
        ],$sql);
        $result->setFetchMode(\PDO::FETCH_ASSOC);
        return $result;
    }
}