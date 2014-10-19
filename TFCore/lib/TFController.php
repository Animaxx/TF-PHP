<?php
/**
 * This is the controller base.
 *
 * Note: Action must only return Dispaly Code or TFView object , should not direct output.
 *
 * @author Animax
 */
class TFController {
    /**
     * Jump to url
     *
     * @param string $url
     */
    public function jumpTo($url){
        @header("location: $url");
    }
    
    /**
     * Jump to Action url
     *
     * @param string $Action
     */
    public function jumpToController($controller, $action=null){
    	global $TFConfig;
    	
    	$url_controller_tag = $TFConfig["url_controller_tag"];
    	$url_action_tag = $TFConfig["url_action_tag"];
    	
    	$url = "?$url_controller_tag=$controller";
    	if ($action != null){
    		$url .= "&$url_action_tag=$action";
    	}
    	
        @header("location: $url");
    }
    
    /**
     * Jump to Action url
     *
     * @param string $Action
     */
    public function jumpToAction($action=null){
    	global $TFConfig;
    	
    	$url_controller_tag = $TFConfig["url_controller_tag"];
    	$url_action_tag = $TFConfig["url_action_tag"];
    	
    	$controller = get_class($this);

    	$url = "?$url_controller_tag=$controller";
    	if ($action != null){
    		$url .= "&$url_action_tag=$action";
    	}    	
        @header("location: $url");
    }

    /**
     * Check the REQUEST value whether exist.
     *
     * @param string $name
     * @return bool
     */
    public function has($name){
        return (isset($_REQUEST[$name]) && $_REQUEST[$name]!="" );
    }

    /**
     * Get the request value
     *
     * @param string $name
     * @param object $default
     * @return object
     */
    public function get($name, $default = ''){
        if (!$this->has($name)){
            return $default;
        }else{
            return $_REQUEST[$name];
        }
    }

    /**
     * Set the value to request list
     *
     * @param string $name
     * @param object $value
     */
    public function set($name, $value){
        $_REQUEST[$name] = $value;
    }
    
    /**
     * Set a cookie
     * 
     * @param string $name
     * @param object $value
     * @param int $lifecycle Default life cycle time is 3 hours
     */
    public function setCookie($name, $value, $lifecycle=10800) {
        if (is_object($value) || is_array($value)) {
            $value = serialize($value);
        }
        $time = time()+$lifecycle;
        setcookie($name, $value, $time);
    }
    
    /**
     * Get a cookie by name
     * 
     * @param string $name
     * @return object
     */
    public function getCookie($name) {
        $returnValue = $_COOKIE[$name];
		if (is_null(unserialize($returnValue)) || unserialize($returnValue) == '') {
			return $returnValue;
		} else {
        	return unserialize($returnValue);
		}
    }
    
    public function make_querystr($list, $wipe_off=null){
            $query_list = $_GET;
            if (!empty($list) && is_array($list) && count($list) >0){
                    foreach ($list as $key => $item){
                            $query_list[$key] = $item;
                    }
            }

            if (!empty($wipe_off) && is_array($wipe_off) && count($wipe_off) >0){
                    foreach ($wipe_off as $item){
                            unset($query_list[$item]);
                    }
            }

            $query_str = $this->_build_query($query_list);

            return "?".$query_str;
    }
    private function _build_query($query_list, $tag=null){
            $query_str = "";
            foreach ($query_list as $key => $item){
                    if ($query_str != "")
                            $query_str.="&";
                    if ( is_string($item) ){
                            if (empty($tag)) {
                                    $query_str .= "$key=$item";
                            } else {
                                    $query_str .= "{$tag}[]=$item";
                            }
                    }
                    if (is_array($item)){
                            $query_str .= $this->_build_query($item, $key);
                    }
            }
            return $query_str;
    }
}

