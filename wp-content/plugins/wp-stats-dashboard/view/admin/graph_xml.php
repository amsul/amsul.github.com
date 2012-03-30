<?php
/**
 * Load graph xml.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 * @deprecated
 */
include_once(dirname(__FILE__) . '/../../../../../wp-load.php'); // load wordpress context.
global $wp_version;
/**
 * Fix XML.
 * @param string $xml
 * @param string $width
 * @param stringn $height
 * @return string xml
 */
function wpsd_set_size($xml = '', $width = '475', $height ='195') {
	global $wp_version;
	if('' != $xml) {

		if($wp_version < 2.7){
			$x = 75;
		} else {
			$x = 35;
		}

		$xml = str_replace('<chart_rect x="90" y="5" height="215" width="570" />',
			'<chart_rect x="'.$x.'" y="5" height="'.$height.'" width="'.$width.'" />', $xml);
	}
	return $xml;
}

/**
 * Set chart type.
 * @param string $xml
 * @param integer $type
 * @return string xml
 */
function wpsd_set_chart_type($xml = '', $type = 0){
	$chart_type = 'line';

	$types = array('line', 'column', 'bar', '3d column', '3d area', 'bubble');

	if($type > 0) {
		$chart_type = $types[$type];
	}

	if('' != $xml){
		$xml = str_replace('<chart_type>line</chart_type>',
			'<chart_type>' . $chart_type . '</chart_type>', $xml);
	}

	return $xml;
}

$blog_id = get_option('wpsd_blog_id');
$cookie_filename = md5($blog_id);
$cache_path = dirname(__FILE__) . '/../../../../cache/';
$cache_dir = $cache_path . 'wp-stats-dashboard';

if(!file_exists($cache_dir)){
	if(is_writable($cache_path)){
		mkdir($cache_dir);
		touch($cache_dir . '/' . $cookie_filename);
	}
}

if(!file_exists($cache_dir . '/' . $cookie_filename)){
	touch($cache_dir . '/' . $cookie_filename);
}

$user = wp_get_current_user();
if($user->caps['administrator']){
	$un = get_option('wpsd_un');
	$pw = get_option('wpsd_pw');
	$chart_type = get_option('wpsd_type');
	$graph_xml_url = "http://dashboard.wordpress.com/wp-includes/charts/stats-data.php?blog={$blog_id}&page=estats&unit=1&width=670&height=265";
	$url ="https://dashboard.wordpress.com/wp-login.php";
	$cookie = $cache_dir . '/' . $cookie_filename;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, "log={$un}&pwd={$pw}&testcookie=1");
	ob_start();
	curl_exec ($ch);
	ob_end_clean();
	curl_close ($ch);
	unset($ch);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($ch, CURLOPT_URL, $graph_xml_url);
	$result = curl_exec ($ch);
	curl_close ($ch);

	if($wp_version < 2.7){
		$xml = wpsd_set_size($result, '475', '195');
	} else {
		$xml = wpsd_set_size($result, 475 / 1.1, 195 / 1.1);
	}

	echo wpsd_set_chart_type($xml, $chart_type);

//	unlink($cookie);
}
?>