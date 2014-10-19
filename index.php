<?php
define("TF_PATH",dirname(__FILE__).DIRECTORY_SEPARATOR.'TFCore'.DIRECTORY_SEPARATOR);
define("APP_PATH",dirname(__FILE__).DIRECTORY_SEPARATOR);

ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.TF_PATH);

include_once 'TFCore'.DIRECTORY_SEPARATOR.'TFApplication.php';

try{
    TFApplication::Main(require(APP_PATH.DIRECTORY_SEPARATOR."Config.php"));
}
catch(Exception $ex){
    TF_dump($ex);
}
