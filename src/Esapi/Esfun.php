<?php

function raise($message , $code = 0 ,$data = [] , $async = false)
{
    \Esapi\Esapi::instance ()->response->setStatus($code);
    $structure = \Esapi\Esapi::instance()->response->structure(
        $code,$message,ES_HASH,$data
    );
    if(ES_DEBUG)
    {
        $structure['debug'] = \Esapi\Debug\Debug::show();
    }
    \Esapi\Console\Event::execute ('EXECUTE_AFTER',$structure);
    \Esapi\Esapi::instance ()->response->setContent( autoConversion ($structure) )->send($async);
}
if(!function_exists('array2json'))
{
    function array2json( array $array )
    {
        return json_encode($array);
    }
}
if(!function_exists('array2xml'))
{
    function array2xml( array $array )
    {
        $func = function($node , $data , $func)
        {
            foreach ($data as $key => $value) {
                if ( is_numeric( $key ) ) {
                    $key = "unknownNode_". (string) $key;
                }
                $key = preg_replace( '/[^a-z0-9\-\_\.\:]/i' ,  '' , $key );
                if ( is_array( $value ) ) {
                    $child = $node->addChild( $key );
                    $func( $child , $value , $func );
                } else {
                    $value = str_replace( '&' ,  '&amp;' , print_r( $value ,  true ) );
                    $node->addChild( $key , $value );
                }
            }
            return $node;
        };
        return call_user_func_array($func,
            [simplexml_load_string( "<?xml version='1.0' encoding='utf-8'?><root />" ), $array,$func]
        )->asXml();
    }
}
if(!function_exists('array2object'))
{
    function array2object( array $array )
    {
        return json2object(json_encode($array));
    }
}
if(!function_exists('json2array'))
{
    function json2array( $json )
    {
        return json_decode($json,true);
    }
}
if(!function_exists('json2xml'))
{
    function json2xml( $json )
    {
        return array2Xml(json_decode($json,true));
    }
}
if(!function_exists('json2object'))
{
    function json2object( $json )
    {
        return json_decode($json);
    }
}
if(!function_exists('xml2array'))
{
    function xml2array( $xml )
    {
        return object2array(simplexml_load_string($xml));
    }
}
if(!function_exists('xml2json'))
{
    function xml2json( $xml )
    {
        return object2json(simplexml_load_string($xml));
    }
}
if(!function_exists('xml2object'))
{
    function xml2object( $xml )
    {
        return simplexml_load_string($xml);
    }
}
if(!function_exists('object2array'))
{
    function object2array( $object )
    {
        return json2array(object2json($object));
    }
}
if(!function_exists('object2json'))
{
    function object2json( $object )
    {
        return json_encode($object);
    }
}
if(!function_exists('object2xml'))
{
    function object2xml( $object )
    {
        return array2xml(object2array($object));
    }
}
if(!function_exists ('autoConversion'))
{
    function autoConversion( $content )
    {
        return call_user_func(sprintf(
            "array2%s",\Esapi\Esapi::config ('app.format')
        ),$content);
    }
}
if( !function_exists('multiSort') )
{
    function multiSort($arr,$key,$sort = 'ASC')
    {
        $w = [];
        foreach ($arr as $k=>$v)
        {
            $w[$k] = $v[$key];
        }
        array_multisort($w,$sort == 'ASC' ? SORT_ASC : SORT_DESC , $arr);
        return $arr;
    }
}
if( !function_exists('val2key') )
{
    function val2key(array $arr , $val_key = '' , $multidimensional = false)
    {
        if(!$arr) return $arr;
        $new = [];
        foreach ($arr as $v)
        {
            if($multidimensional)
                $new[$v[$val_key]][] = $v;
            else
                $new[$v[$val_key]] = $v;
        }
        return $new;
    }
}

if( !function_exists( 'shell_cron' ) )
{
    function shell_cron( $cron_file = '' , $noHup = false , $paramString = '' )
    {
        $data = NULL;

        if( $noHup )
        {
            fastcgi_finish_request();
        }

        $command = sprintf(
            "cd %s && %s/php %s",
            str_replace(basename($cron_file),'',$cron_file),
            PHP_BINDIR,$cron_file
        );

        if(!empty($paramString) && is_array($paramString))
        {
            foreach ($paramString as $k=>$p)
            {
                $data[$k] = [];
                system(sprintf("%s %s &",$command ,$p),$data[$k]);
            }
        }
        else
        {
            system(sprintf("%s %s",$command ,$paramString),$data);
        }

        return $data;
    }
}