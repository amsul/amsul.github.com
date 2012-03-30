<?php
if(!class_exists('Services_JSON')) {
	include_once(realpath(dirname(__FILE__).'/..') . '/Services_JSON.php');
}
/**
 * WPSDStats.
 *
 * @author dligthart <info@daveligthart.com>
 * @version 0.5
 * @package wp-stats-dashboard
 */
class WPSDStats {

	/**
	 * cache_date
	 * 
	 * @var mixed
	 * @access public
	 */
	var $cache_date;

	/**
	 * update_cache
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access public
	 */
	var $update_cache = false;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		global $wpsd_update_cache;	// mainly used for cron.

		$this->cache_date = get_option('wpsd_cache_date');

		if (date('Y-m-d') != $this->cache_date || $wpsd_update_cache === true) {

			$this->update_cache = true;
		}

		if ($_REQUEST['wpsd_update_cache']) {

			$this->update_cache = true;
		}
	}
	
	/**
	 * WPSDStats function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDStats() {
		
		$this->__construct();
	}

	/**
	 * isOutdated function.
	 * 
	 * @access public
	 * @return void
	 * @deprecated
	 */
	function isOutdated() {
		return $this->update_cache;
	}

	/**
	 * set_cache function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 * @deprecated
	 */
	function set_cache($key, $value) {
		update_option('wpsd_'. $key, $value);
	}

	/**
	 * get_cache function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return void
	 * @deprecated
	 */
	function get_cache($key) {
		return get_option('wpsd_' . $key);
	}

	/**
	 * cache_updated function.
	 * 
	 * @access public
	 * @return void
	 * @deprecated
	 */
	function cache_updated() {
		update_option('wpsd_cache_date', date('Y-m-d'));
	}
	
	/**
	 * get_cached function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return mixed
	 */
	function get_cached($key) {
		
		$arr = get_option('wpsd_cache');
		
		if(null == $arr || !is_array($arr)) {
			
			return false;
		}
		
		return $arr[$key];
	}
	
	/**
	 * update_cache function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @return boolean
	 */
	function update_cache($key, $value) {
		
		$arr = get_option('wpsd_cache');
		
		if(null == $arr || !is_array($arr)) {
			
			$arr = array();
		}
		
		$arr[$key] = $value;
		
		return update_option('wpsd_cache', $arr);
	}

	/**
	 * is_cache_outdated function.
	 * 
	 * @access public
	 * @param mixed $cache_key
	 * @param mixed $key
	 * @return boolean
	 */
	function is_cache_outdated($cache_key, $key) {
		
		if('' == $cache_key || '' == $key) return true;
		
		if(isset($_REQUEST['wpsd_update_cache'])) return true;
	
		$arr = get_option("wpsd_cache_dates_{$cache_key}");
		
		if(is_array($arr) && isset($arr[$key])) {
			
			if($arr[$key] == date('Y-m-d')) {
				
				return false;
			}
		} 
		
		return true;
	}
	
	/**
	 * updated_cache function.
	 * 
	 * @access public
	 * @param mixed $cache_key
	 * @param mixed $key
	 * @return void
	 */
	function updated_cache($cache_key, $key) {
		
		$arr = get_option("wpsd_cache_dates_{$cache_key}");
		
		if(!is_array($arr)) {
		
			$arr = array();
		}
		
		$arr[$key] = date('Y-m-d');
			
		update_option("wpsd_cache_dates_{$cache_key}", $arr);
	}


	/**
	 * getHost function.
	 * 
	 * @access public
	 * @param mixed $parseUrl
	 * @return void
	 */
	function getHost($parseUrl) {

		return trim($parseUrl["host"] ? $parseUrl["host"] : array_shift(explode('/', $parseUrl["path"], 2)));
	}

	/**
	 * getNormalizedUrl function.
	 * 
	 * @access public
	 * @param mixed $uri
	 * @return void
	 */
	function getNormalizedUrl($uri) {

		$parseUrl = parse_url(trim($uri));

		return $parseUrl["scheme"] . '://' . $this->getHost($parseUrl);
	}

	/**
	 * fetchDataRemote function.
	 * 
	 * @access public
	 * @param mixed $url
	 * @param bool $follow. (default: false)
	 * @param int $agent. (default: 1)
	 * @param bool $cloak. (default: false)
	 * @param string $cookie. (default: '')
	 * @return void
	 */
	function fetchDataRemote($url, $follow = false, $agent = 1, $cloak = false, $cookie = '') {

		if ('' == $url) return false;
		
		if($cloak) return $this->cloakCurl($url, $cookie);

		$xml = '';

		if (function_exists('curl_init')) {

			$ch = curl_init();

			$timeout = 2;

			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			if('' != $cookie) {
			
				curl_setopt($curl, CURLOPT_COOKIE, $cookie);
			}

			if ($follow) {

				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			}

			switch ($agent) {
			default:
			case 1:
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
				break;
			case 2:
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2.8) Gecko/20100722 BTRS86393 Firefox/3.6.8 ( .NET CLR 3.5.30729; .NET4.0C)");
				break;
			case 3:
				curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
				break;
			}

			$xml = curl_exec($ch);

			curl_close($ch);
		}
		else {

			$xml = file_get_contents($url);
		}

		return $xml;
	}
	
	/**
	 * cloakCurl function.
	 * 
	 * @access public
	 * @param mixed $url
	 * @param string $cookie. (default: '')
	 * @return void
	 */
	function cloakCurl($url, $cookie = '') {
	
		$curl = curl_init();

		// setup headers - used the same headers from Firefox version 2.0.0.6
		// below was split up because php.net said the line was too long. :/
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: "; //browsers keep this blank.
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 3);
		
		if('' != $cookie) {
		
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		
		$html = curl_exec($curl); //execute the curl command
	
		curl_close($curl); //close the connection

		return $html; //and finally, return $html
	}
}
?>