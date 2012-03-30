<?php

if(!class_exists('Services_JSON')) {
	include_once(realpath(dirname(__FILE__).'/..') . '/Services_JSON.php');
}

/**
 * PostRank.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDPostRank extends WPSDStats {

	var $appKey = 'wp-stats-dashboard';
	var $appName = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
	var $channel = null;
	
	function WPSDPostRank() {
		
		parent::WPSDStats();
	}
	
	function getHomeAddress() {
		return 'http://www.postrank.com/';
	}

	/**
	 * @param $method
	 * @param $url
	 * @param $vars
	 * @return unknown_type
	 */
	function makeRequest( $method, $url, $vars ) {
		
		// if the $vars are in an array then turn them into a usable string
		if( is_array( $vars ) ){

			$vars = implode( '&', $vars );
		}
	
	    curl_setopt( $this->channel, CURLOPT_HEADER, false);
		curl_setopt( $this->channel, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt( $this->channel, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);	
		curl_setopt( $this->channel, CURLOPT_URL, $url );
		
		if ( strtolower( $method ) == 'post' ) {
			
			curl_setopt( $this->channel, CURLOPT_POST, true );
			
			curl_setopt( $this->channel, CURLOPT_POSTFIELDS, $vars );
		}
		
		// return data
		return curl_exec( $this->channel );
	}

	/**
	 * @param $url
	 * @return unknown_type
	 */
	function getUrl($url, $vars, $method='post') { // use cURL if possible
		
		$content = '';
		
		if (function_exists('curl_init')) {
			
			$this->channel = curl_init( );
	      		
	        $content = $this->makeRequest( $method, $url, $vars );
			
	        $errNumber = curl_errno($this->channel);
	        
			$errMsg  = curl_error($this->channel);

			curl_close($this->channel);
			
			// Try something else.
			if('' == $content) {

				$content = file_get_contents($url);
			}
		
			//return intval($errNumber) == 0 ? $content : false;

		} else {
			
			$content = file_get_contents($url);
		}
		
		return $content;
	}

	/**
	 * @param $targetUrl
	 * @return unknown_type
	 */
	function query($targetUrl) {
		
		$requestUrl = "http://api.postrank.com/v1/postrank?appkey=" . $this->appKey . '&format=json';

		$resp = $this->getUrl($requestUrl, 'url[]=' . $targetUrl . '&');
		
		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

		$data = $json->decode($resp);

		return $data;
	}
	
	/**
	 * Get rank.
	 * 
	 * @return integer rank
	 * @access public
	 */
	function getRank() {
		return $this->get_cache('post_rank');
	}
	
	/**
	 * @param $targetUrl
	 * @param $raw
	 * @return unknown_type
	 */
	function getPostRank($targetUrl, $raw = false) {
		
		if($this->isOutdated() && '' != $targetUrl) {
						
			$targetUrl = $this->getNormalizedUrl($targetUrl) . '/';
	
			$resp = $this->query($targetUrl);
			
			$rank = $resp[$targetUrl]['postrank'];
			
			$this->set_cache('post_rank', $rank);
		}
		
		return $this->get_cache('post_rank');
	}
}
?>