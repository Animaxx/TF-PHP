<?php

/**
 * Do the current or specify Controller
 *
 * @param array $args Parameters required for executive the Action.
 * @param string $controller Controller name, if null will use the current Controller.
 * @param string $action Action name,  if null will use the current Action.
 * @return string After action process return the string for the interface display.
 */
function TF_DoAction($args = null, $controller=null,$action=null) {
    global $GLOBALS, $TFConfig;
    
    $controller_object = TF_GetController($controller);
    
    if (isset($TFConfig['global_calss']) && isset($TFConfig['global_before_action'])) {
    	@call_user_func($TFConfig['global_calss']."::".$TFConfig['global_before_action'], $controller_object);
    }
    
    $web_output_data = '';

    if (isset($action)){
        if (!method_exists($controller_object, $action))
            TF_debug("Can't found Action {{$action}} in {".get_class($controller_object)."}");
        $web_output_data =$controller_object->$action($args);
    }else{
        if (isset($_REQUEST[$TFConfig["url_action_tag"]])){
            if (!method_exists($controller_object, $_REQUEST[$TFConfig['url_action_tag']]))
                TF_debug("Can't found Action {{$_REQUEST[$TFConfig['url_action_tag']]}} in controller {".get_class($controller_object)."}");
            $web_output_data =$controller_object->$_REQUEST[$TFConfig["url_action_tag"]]($args);
        }else{
            if (!method_exists($controller_object, $TFConfig['default_action']))
                TF_debug("Can't found Action {{$TFConfig['default_action']}} in controller {".get_class($controller_object)."}");
            $web_output_data = $controller_object->$TFConfig["default_action"]($args);
        }
    }
    
    if (isset($TFConfig['global_calss']) && isset($TFConfig['global_after_action'])) {
    	$second_output_data = @call_user_func($TFConfig['global_calss']."::".$TFConfig['global_after_action'], $controller_object, $web_output_data);
        if ($second_output_data && $second_output_data != "") {
           $web_output_data = $second_output_data;
        }
    }
    
    if (is_string($web_output_data)){
        echo $web_output_data;
    } else if (is_object($web_output_data) && $web_output_data instanceof ITFView){
        $web_output_data->desplayView();
    } else {
        echo "<h1>The Action not have correct return.</h1>";
    }
}

/**
 * Get the current or specify Controller
 *
 * @param String $controller Controller name, if null will get the current Controller.
 * @return TFController If controller does not exist will be return FALSE ;
 */
function TF_GetController($controller = null) {	
    global $GLOBALS, $TFConfig;
    
    if (!isset($controller) ||  empty($controller)) {
        $controller = $TFConfig["default_controller"];
        
        if (isset($_REQUEST[$TFConfig["url_controller_tag"]]))
            $controller = $_REQUEST[$TFConfig["url_controller_tag"]];
    }

    if (isset($GLOBALS['inst_controller'][$controller]) && !empty($GLOBALS['inst_controller'][$controller])){
        return $GLOBALS['inst_controller'][$controller];
    }
    $cont = TF_Class($controller,null,$TFConfig['controller_folder']);
    
    if (!$cont){
        TF_debug("Can't found the controller { $controller } .");
    }

    $GLOBALS['inst_controller'][$controller] = $cont;
    
    return $GLOBALS['inst_controller'][$controller];
}

/** 
 * Get all folders in the specified directory.
 * 
 * @param String $var specified directory 
 */
function __getFolders($var){
    global $GLOBALS, $TFConfig;

    if (isset($GLOBALS['get_folders']) && isset($GLOBALS['get_folders'][md5($var)])){
        return $GLOBALS['get_folders'][md5($var)];
    }
	
    $factoryCache = TF_CacheFactory::GetCache("_folders");
    $cache_value = $factoryCache->get(md5($var), FALSE);
   	if (isset($factoryCache) && isset($cache_value) && $cache_value){
            return $cache_value;
	}
    if (strripos($var,"\\") != (strlen($var) - 1) && strripos($var,"/") != (strlen($var) - 1))
        $var .= DIRECTORY_SEPARATOR;

    $folderlist = array();
    if (@is_dir($var))
        $folderlist[] = $var;
    else
        return $folderlist;

    $folders = @opendir($var);
    
    while ($item = readdir($folders)) {
        if ($item == "." || $item == "..")
            continue;
        $need_ignore = false;
        foreach ($TFConfig['autoload_ignore'] as $ignore_item){
            if ($item == $ignore_item){
                $need_ignore = true;
                continue;
            }
        }
        if ($need_ignore) continue;
        
        if (is_dir($var.$item)){
            $folderlist = array_merge($folderlist, __getFolders($var.$item.DIRECTORY_SEPARATOR));
        }
    }
	
    if (empty($GLOBALS['get_folders'])) {
    	$GLOBALS['get_folders'] = array();
    }
    
    $GLOBALS['get_folders'][md5($var)] = $folderlist;
    $factoryCache->set(md5($var), $folderlist);
	
    return $folderlist;
}
/**
 * Get the application folder list
 *
 * @return array 
 */
function TF_GetIncludeFolders(){	
    global $GLOBALS, $TFConfig;

    if (isset($GLOBALS['include_folders'])){
        return $GLOBALS['include_folders'];
    }

    $folderlist = __getFolders(APP_PATH);
    
    $folderlist = array_merge($folderlist,__getFolders(TF_PATH));

    $GLOBALS['include_folders'] = $folderlist;
    return $GLOBALS['include_folders'];
}

/**
 * Use require import file to application
 *
 * @param string $sfilename File name
 * @param bool $auto_search Whether to use auto
 * @param bool $error_ourput
 * @return bool If file can be loaded than return TRUE, else return FALSE .
 */
function import($sfilename, $auto_search = TRUE, $error_ourput = FALSE) {
    global $GLOBALS, $TFConfig;
	
    if (isset($GLOBALS['import_file'][md5($sfilename)])){
    	return TRUE;
    }
    if (TRUE == @is_readable($sfilename) && !is_dir($sfilename)) {
        require($sfilename); 
        $GLOBALS['import_file'][md5($sfilename)] = TRUE;
        return TRUE;
    } else {
        foreach ($TFConfig['autoload_extname'] as $item){
            if (TRUE == @is_readable($sfilename.$item) && !is_dir($sfilename.$item)){
                require($sfilename.$item);
                $GLOBALS['import_file'][md5($sfilename)] = TRUE;
                return TRUE;
            }
        }

        if (TRUE == $auto_search) {
            $ext_folders = array();
            if (isset($TFConfig['autoload_folder']) && !empty($TFConfig['autoload_folder']) && is_array($TFConfig['autoload_folder'])){
                $ext_folders = $TFConfig['autoload_folder'];
            }
            foreach (array_merge(TF_GetIncludeFolders(),$ext_folders)  as $include_path) {
                if (isset($GLOBALS['import_file'][md5($include_path . DIRECTORY_SEPARATOR . $sfilename)])){
                    return TRUE;
                }
                if (is_readable($include_path . DIRECTORY_SEPARATOR . $sfilename) && !is_dir($include_path . DIRECTORY_SEPARATOR . $sfilename)) {
                    require($include_path . DIRECTORY_SEPARATOR . $sfilename);
                    $GLOBALS['import_file'][md5($include_path . DIRECTORY_SEPARATOR . $sfilename)] = TRUE;
                    return TRUE;
                }

                foreach ($TFConfig['autoload_extname'] as $item){
                    if (isset($GLOBALS['import_file'][md5($include_path . DIRECTORY_SEPARATOR . $sfilename.$item)]))
                        return TRUE;
                    if (is_readable($include_path . DIRECTORY_SEPARATOR . $sfilename.$item) && !is_dir($include_path . DIRECTORY_SEPARATOR . $sfilename.$item) ) {
                        require($include_path . DIRECTORY_SEPARATOR . $sfilename.$item);
                        $GLOBALS['import_file'][md5($include_path . DIRECTORY_SEPARATOR . $sfilename.$item)] = TRUE;
                        return TRUE;
                    }
                }
            }
        }
    }
    
    if (TRUE == $error_ourput)
        TF_debug("The file {$sfilename} can't find. ");
    return FALSE;
}

/**
 * Instantiation the class
 *
 * @param string $class_name The class name.
 * @param array $args Call the class required parameters.
 * @param string $sdir The class file path.
 * @param bool $err_stop If an error during loading, can be stop.
 * @return bool If can instantiation the class then return the Class Object else return FALSE.
 */
function TF_Class($class_name, $args = null, $sdir = null, $err_stop = TRUE){
    global $GLOBALS, $TFConfig;
    
    if(preg_match('/[^a-z0-9\-_.]/i', $class_name))TF_debug("{$class_name} Class name exception ,please check. ", $err_stop);
    if(isset($GLOBALS["inst_class"][$class_name]))return $GLOBALS["inst_class"][$class_name];
    
    if(isset($sdir) ){    	
        $import_file_result = FALSE;
        if (strripos($sdir,"\\") != (strlen($sdir) - 1) && strripos($sdir,"/") != (strlen($sdir) - 1))
            $sdir .= DIRECTORY_SEPARATOR;

        if (import($sdir.$class_name)){
            $import_file_result = TRUE;
        }
        
        if (!$import_file_result)
            return FALSE;
    }    
	
    $has_define = FALSE;
    
    
    if(class_exists($class_name, false) || interface_exists($class_name, false)){
        $has_define = TRUE;
    }else{
        if (import($class_name)){
            $has_define = TRUE;
        }
    }
    if(FALSE != $has_define){
        $GLOBALS["inst_class"][$class_name] = new $class_name($args);
        return $GLOBALS["inst_class"][$class_name];
    }
    TF_debug("{$class_name} Can't instantiation the class, class definition does not exist, please check it.", $err_stop);
    return FALSE;
}

/**
 * Output the debug Message. 
 *
 * @param string $msg
 * @param bool $stop
 */
function TF_debug($msg, $stop = TRUE){
    global $TFConfig;

    if ($TFConfig['mode'] == 'release'){
        error_log($msg);
        if(TRUE == $stop)exit;
        return;
    }
    $traces = debug_backtrace();
    $bufferabove = ob_get_clean();
    require_once(TF_PATH."lib".DIRECTORY_SEPARATOR."TFdebug.Template");
    if(TRUE === $stop)exit;
}

/**
 * Format output a object detail information.
 *
 * @param Object $obj The object of the specified output
 * @param bool $debug If want to use the debug mode output, set to TRUE.
 * @param bool $stop
 */
function TF_dump($obj, $debug = TRUE, $stop = TRUE){
    global $TFConfig;

    if ($TFConfig['mode'] == 'release'){
        return ;
    }

    $content = htmlspecialchars(print_r($obj, true));
    $content = "<div align=left><pre>$content</pre></div>";
    
    if ($debug)
        TF_debug($content,$stop);
    else
        echo $content;

    return ;
}

/**
 * System Loading Zend or Other Setting
 * 
 */
function TF_LoadingSetting(){
    global $TFConfig;
    
    $used_zend = false;
    foreach ($TFConfig["zend"] as $key => $value) {
        if ($value) {
            $used_zend = true;
            break;
        }
    }
    if ($used_zend) {
        include_once TF_PATH."Zend".DIRECTORY_SEPARATOR."Loader.php";
    }
    if ($TFConfig["zend"]["db"]) {
        Zend_Loader::loadFile("Db.php", TF_PATH."Zend", true);
        $params = array (
            'host'     => $TFConfig["db"]['host'],
            'username' => $TFConfig["db"]['login'],
            'password' => $TFConfig["db"]['password'],
            'dbname'   => $TFConfig["db"]['database']
        );
        $database_type = "";
        switch ($TFConfig["db"]['database_type']) {
            case "mysql":
                $database_type="PDO_MYSQL";
                break;
            default:
                break;
        }
        
        $db = Zend_Db::factory($database_type, $params);  

        require_once 'Zend/Db/Table.php';
        Zend_Db_Table::setDefaultAdapter($db);
    }
    
//    if ($TFConfig["zend"]["dom"]) {
//        Zend_Loader::loadFile("Query.php", TF_PATH."Zend".DIRECTORY_SEPARATOR."Dom", true);
//    }
//    if ($TFConfig["zend"]["http_client"]) {
//        Zend_Loader::loadFile("Client.php", TF_PATH."Zend".DIRECTORY_SEPARATOR."Http", true);
//    }
}


function __autoload($class_name){
    
    $successLoading = false;
    
    if (!import($class_name)){
        $_class = strtolower($class_name);

        if (substr($_class, 0, 16) === 'smarty_internal_' || $_class == 'smarty_security') {
            $file = APP_PATH . "TFCore" . DIRECTORY_SEPARATOR . "Smarty" . DIRECTORY_SEPARATOR . "sysplugins" . DIRECTORY_SEPARATOR . $_class . '.php';

            if (TRUE == @is_readable($file)) {
                include $file;
                $successLoading = true;
            } else 
                $successLoading = FALSE;
        }
    	$successLoading = FALSE;
    }
    
    if (!$successLoading) {
        global $TFConfig;
        if ($TFConfig["zend"]["autoload_zend"]) {
            Zend_loader::loadClass($class_name);
        }
    }
    
    return $successLoading;
    //TF_debug("cannot found this class { $class_name }");
}

function __mergerConfig($preconfig, $useconfig = null){
    $nowconfig = $preconfig;
    if (is_array($useconfig)) {
        foreach ($useconfig as $key => $val) {
            if (is_array($useconfig[$key])) {
                @$nowconfig[$key] = is_array($nowconfig[$key]) ? __mergerConfig($nowconfig[$key], $useconfig[$key]) : $useconfig[$key];
            } else {
                @$nowconfig[$key] = $val;
            }
        }
    }
    return $nowconfig;
}

