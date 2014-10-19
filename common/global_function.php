<?php
/**
 * Description of global
 * @author Animax
 */
class global_function {
	/*
	 * Do something global operate.
	 */
	public static function beforeAction($controller){
            // Doing some global operation here.
	}
        
	public static function afterAction($controller, $output) {
		if($output instanceof TFView){
			if (isset($_COOKIE['current_user'])) {
				if (is_null(unserialize($_COOKIE['current_user'])) || unserialize($_COOKIE['current_user']) == '') {
					$user = $_COOKIE['current_user'];
				}else{
					$user = unserialize($_COOKIE['current_user']);
				}
				
				$welcome = '<img src='.$root_url.'"images/icon-user.png" alt="" /> Welcome ';
				$signout = ' <a href="?a=signout">Sign Out</a>';
				if (is_array($user)) {
					if ($user['DisplayName'] != '') {
						$output->addDisplayVar("welcome", $welcome.$user['DisplayName']."&nbsp;&nbsp;|&nbsp;&nbsp;".$signout);
					} else {
						$output->addDisplayVar("welcome", $welcome.$user['LoginName']."&nbsp;&nbsp;|&nbsp;&nbsp;".$signout);
					}
				} else {
					$output->addDisplayVar("welcome", $welcome.$user."&nbsp;&nbsp;|&nbsp;&nbsp;".$signout);
				}
			} else {
				$signin = '<a href="?a=signin">Sign In</a>';
				$signup = '<a href="?a=subscribe">Sign Up</a>';
				$output->addDisplayVar("welcome", $signin."&nbsp;&nbsp;|&nbsp;&nbsp;".$signup);
			}
		}
		global $TFConfig;
		$controller_name =  $_REQUEST[$TFConfig["url_controller_tag"]];
		if (empty($controller_name)){
			$controller_name = $TFConfig["default_controller"];
		}

		$action_name =  $_REQUEST[$TFConfig["url_action_tag"]];
		if (empty($action_name)){
			$action_name = $TFConfig["default_action"];
		}
		$output->addDisplayVar("current_controller",$controller_name);
		$output->addDisplayVar("action_name",$action_name);
		
		return $output;
	}

	/**
	 * get the client ip
	 *
	 * return @return String
	 */
	public static function getRealIpAddr()
	{
            if (!emptyempty($_SERVER['HTTP_CLIENT_IP'])){
                    $ip=$_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (!emptyempty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else{
                    $ip=$_SERVER['REMOTE_ADDR'];
            }
            return $ip;
	}

	/**
	 * Generate The Random String
	 *
	 * @param int $length
	 * return @return String
	 */
	public static function generateTheRandomString($length = 6){
		$c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789^*~!#+-";
		srand((double)microtime()*1000000);
		for($i=0; $i<$l; $i++) {
			$rand.= $c[rand()%strlen($c)];
		}
		return $rand;
	}

	/**
	 * Verify email address
	 * @param String $email
	 * @param String $test_mx
	 *
	 * return @return Bool
	 */
	public static function isEmailAddress($email, $test_mx = false){
		if(eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)){
			if($test_mx){
				list($username, $domain) = split("@", $email);
				return getmxrr($domain, $mxrecords);
			}else
			return true;
		} else
		return false;
	}

	/**
	 * Resize the image
	 * 
	 * @param String $filename path to the image
	 * @param String $tmpname temporary path to thumbnail 
	 * @param int $xmax
	 * @param int $ymax
	 */
	public static function resizeImage($filename, $tmpname, $xmax, $ymax)
	{
		$ext = explode(".", $filename);
		$ext = $ext[count($ext)-1];

		if($ext == "jpg" || $ext == "jpeg")
		$im = imagecreatefromjpeg($tmpname);
		elseif($ext == "png")
		$im = imagecreatefrompng($tmpname);
		elseif($ext == "gif")
		$im = imagecreatefromgif($tmpname);

		$x = imagesx($im);
		$y = imagesy($im);

		if($x <= $xmax && $y <= $ymax)
		return $im;

		if($x >= $y) {
			$newx = $xmax;
			$newy = $newx * $y / $x;
		}
		else {
			$newy = $ymax;
			$newx = $x / $y * $newy;
		}

		$im2 = imagecreatetruecolor($newx, $newy);
		imagecopyresized($im2, $im, 0, 0, 0, 0, floor($newx), floor($newy), $x, $y);
		return $im2;
	}
        
	public static function iCalendar($datestart,$dateend,$filename='iCalendar',$address='',$description='',$uri='',$summary='') {
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);

		echo "BEGIN:VCALENDAR
		VERSION:2.0
		PRODID:-//hacksw/handcal//NONSGML v1.0//EN
		CALSCALE:GREGORIAN
		BEGIN:VEVENT
		DTEND:".date('Ymd\THis\Z', $dateend)."
		UID:".date('Ymd\THis\Z', uniqid())."
		DTSTAMP:".date('Ymd\THis\Z', time())."
		LOCATION:".preg_replace('/([\,;])/','\\\$1', $address)."
		DESCRIPTION:".preg_replace('/([\,;])/','\\\$1', $description)."
		URL;VALUE=URI:".preg_replace('/([\,;])/','\\\$1', $uri)."
		SUMMARY:".preg_replace('/([\,;])/','\\\$1', $summary)."
		DTSTART:".date('Ymd\THis\Z', $datestart)."
		END:VEVENT
		END:VCALENDAR";
		
		die();
		exit;
	}
}


