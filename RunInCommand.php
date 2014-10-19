<?php

define("TF_PATH",dirname(__FILE__).DIRECTORY_SEPARATOR.'TFCore'.DIRECTORY_SEPARATOR);
define("APP_PATH",dirname(__FILE__).DIRECTORY_SEPARATOR);
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.TF_PATH);

require_once TF_PATH . "lib". DIRECTORY_SEPARATOR ."ITFCache.php";
require_once TF_PATH . "lib". DIRECTORY_SEPARATOR ."TF_CacheFactory.php";
require_once TF_PATH . "TFCoreFunctions.php";

global $TFConfig;
$TFConfig = __mergerConfig(require(TF_PATH . DIRECTORY_SEPARATOR. "TFConfig.php"), require(APP_PATH.DIRECTORY_SEPARATOR."Config.php"));

include_once TF_PATH."Zend".DIRECTORY_SEPARATOR."Loader.php";
TF_LoadingSetting();

// TODO: 