<?php
/*
 * ^_^ 无需其他代码
 * Github: https://github.com/wss-dev/esapi
 * Website: https://esapi.wss.me
 * Author : huakaiquan@qq.com
 * Listen : MIT
 * */

define('ES_APP',sprintf("%s%s%s%s",
    dirname(__DIR__),
    DIRECTORY_SEPARATOR,'test',DIRECTORY_SEPARATOR
));

require_once realpath("../src/Esapi/Start.php");