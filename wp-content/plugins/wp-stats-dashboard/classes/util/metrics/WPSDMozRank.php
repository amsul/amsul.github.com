<?php
/**
 * Linkscape moz rank.
 * @author dligthart
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDMozRank extends WPSDStats {

	var $access_id = false;
	var $secret_key = false;
	var $appName = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';

	function WPSDMozRank() {
		
		parent::WPSDStats();

		$this->access_id = 'member-378e914254';
		
		$this->secret_key = 'bc17675caad53a2e363a8d927f6724d7';
	}

	function getHomeAddress() {
		return 'http://www.seomoz.org/toolbox/pagerank?wurl=' . get_bloginfo('url');
	}

	function getUrl($url) { // use cURL if possible
		//	$safe_mode =  ini_get('safe_mode');

		if (function_exists('curl_init')) {

			$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // any encoding
			CURLOPT_USERAGENT      => $this->appName, // App name - set in the constructor
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 5,      // timeout on connect
			CURLOPT_TIMEOUT        => 5,      // timeout on response
			CURLOPT_MAXREDIRS      => 3,       // stop after 3 redirects
			);

			$ch = curl_init( $url );
				
			foreach($options as $key=>$value) {
				curl_setopt($ch, $key, $value);
			}

			//php5.
			//curl_setopt_array($ch, $options);

			$content = curl_exec($ch);
			$errNumber = curl_errno($ch);
			$errMsg  = curl_error($ch);

			//echo $content;

			curl_close( $ch );
				
			// Try something else.
			if('' == $content) {
				//$content = @file_get_contents($url);
			}

			return intval($errNumber) == 0 ? $content : false;

		} else {
			
			return @file_get_contents($url);
		}
	}
	
	/**
	 * 
	 * @param unknown_type $timestamp
	 * @return string
	 */
	function generateSignature($timestamp) {
		//$timestamp = isset($timestamp) ? $timestamp : time() + 600; // 10 minutes into the future
		$hash = hash_hmac("sha1", $this->access_id . "\n" . $timestamp, $this->secret_key, true);
		return '&Signature=' . urlencode(base64_encode($hash));
	}

	/**
	 * Query mozrank with 30 minutes expire time.
	 * @param $apiName
	 * @param $targetUrl
	 * @return unknown_type
	 */
	function query($apiName, $targetUrl) {
		$timestamp = time() + 1600;
		$targetUrl = urlencode($targetUrl);
		$requestUrl = "http://lsapi.seomoz.com/linkscape/{$apiName}/{$targetUrl}?AccessID={$this->access_id}&Expires={$timestamp}" . $this->generateSignature($timestamp);
		$resp = $this->fetchDataRemote($requestUrl);
		return $resp ? json_decode($resp) : false;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function getRank() {
		return $this->get_cache('moz_rank');
	}
	
	/**
	 * 
	 * @param $targetUrl
	 * @param $raw
	 * @return unknown_type
	 */
	function mozRank($targetUrl, $raw = false) {

		if($this->isOutdated()) {
			$targetUrl = str_replace('http://', '', $targetUrl);

			$targetUrl = str_replace('https://', '', $targetUrl);

			$resp = $this->query('mozrank', $targetUrl);

			$rank = ($resp && isset($resp->umrr)) ? $raw ? $resp->umrr : $resp->umrp : false;

			$this->set_cache('moz_rank', $rank);
		}
		
		return $this->get_cache('moz_rank');
	}
}
?>