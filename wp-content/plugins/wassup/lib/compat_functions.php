<?php
/**
 * @package WassUP
 * @subpackage compat_functions.php module
 */
/**
 * compat_functions.php 
 * Description: Emulate some functions from PHP 5.2+ and Wordpress 2.6+ for
 *   backwards compatibility with PHP 4.3+ and Wordpress 2.2+, respectively
 * @author:	Helene D. <http://techfromhel.com>
 * @version:	0.3 - 2010-09-13
 * @since Wassup 1.8
 */

/**
 * Convert simple JSON data into a PHP object (default) or associative 
 *   array. Emulates 'json_decode' function from PHP 5.2+ 
 * @author: Helene Duncker <http://techfromhel.com>
 * @param string,boolean
 * @return (array or object)
 */
if (!function_exists('json_decode')) {
	function json_decode($json,$to_array=false) { 
		$x=false;
		if (!empty($json) && strpos($json,'{"')!==false) {
			$out = '$x='.str_replace(array('{','":','}'),array('array(','"=>',')'),$json);
			eval($out.';');
			if (!$to_array) $x = (object) $x;
		}
		return $x;
	} //end function json_decode 
}

//'microtime_float' replicates microtime(true) from PHP5
if (!function_exists('microtime_float')) {
	function microtime_float() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

if (!function_exists('admin_url')) {
	function admin_url($admin_file="") {
		$adminurl = get_bloginfo('wpurl')."/wp-admin/".$admin_file;
		return $adminurl;
	} //end function admin_url
}

/**
 * Output the URL of a Wordpress plugin directory. Emulates 'plugins_url' 
 *   function from Wordpress 2.6+
 * @param string
 * @return string
 */
if (!function_exists('plugins_url')) {
	function plugins_url($plugin_file="") {
		if (defined('WP_CONTENT_URL') && defined('WP_CONTENT_DIR') && strpos(WP_CONTENT_DIR,ABSPATH)===FALSE) {
			$pluginurl = rtrim(WP_CONTENT_URL,"/")."/plugins/".$plugin_file;
		} else {
			$pluginurl = get_bloginfo('wpurl')."/wp-content/plugins/".$plugin_file;
		}
		return $pluginurl;
	} //end function
}
?>
