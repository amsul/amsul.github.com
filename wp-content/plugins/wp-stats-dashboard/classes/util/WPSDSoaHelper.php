<?php
/**
 * Soa Helper.
 * @author dligthart <info@daveligthart.com>
 * @package wp-stats-dashboard
 * @subpackage util
 * @version 0.3
 */
class WPSDSoaHelper {

	/**
	 * @var string url to master.
	 */
	var $service_url;
	
	/**
	 * WPSDSoaHelper function.
	 * 
	 * @access public
	 * @param mixed $service_url
	 * @return void
	 */
	function WPSDSoaHelper($service_url = '') {

		if('' == $service_url) {
			
			$service_url = get_bloginfo('wpurl') . '/xmlrpc.php';			
		}

		$this->service_url = $service_url;
	
		add_filter('http_request_args', array($this, 'http_request_args'), 100, 1);
		
	//	add_action('http_api_curl', array($this, 'http_api_curl'), 100, 1);
	}
	
	/**
	 * http_request_args function. Override request timeout.
	 * 
	 * @access public
	 * @param mixed $r
	 * @return void
	 */
	function http_request_args($r) {
		$r['timeout'] = 180;
		return $r;
	}

	/**
	 * http_api_curl function.
	 * 
	 * @access public
	 * @param mixed $handle
	 * @return void
	 */
	function http_api_curl($handle) {
	
		curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 180);
		curl_setopt( $handle, CURLOPT_TIMEOUT, 180);
	}

	/**
	 * getServiceUrl function.
	 * 
	 * @access public
	 * @return void
	 */
	function getServiceUrl() {

		return $this->service_url;
	}

	/**
	 * rpc_call function.
	 * 
	 * @access public
	 * @param string $method. (default: '')
	 * @param array $params. (default: array())
	 * @return void
	 */
	function rpc_call($method = '', $params = array() ) {
	
		$request = new WP_Http();
		
		if(function_exists('xmlrpc_encode_request')) { 
			
			$params = xmlrpc_encode_request($method, $params);
		} 
	
		return $request->request($this->getServiceUrl(), array('method' => 'POST', 'body' => $params));

	}

	/**
	 * Decode response .
	 *
	 * @param unknown_type $response
	 * @return unknown_type
	 * @access private
	 */
	function rpc_decode($response) {
		
		return xmlrpc_decode($response['body']);
	}
	
	/**
	 * getStats function.
	 * 
	 * @access public
	 * @return array stats
	 */
	function getStats($key, $type = 1) {
			
		$args = array('key' => $key, 'type' => $type);
		
		return ( $this->rpc_decode( 
			$this->rpc_call('wpsd.getStats', $args) ));
	}
	
	/**
	 * getStatsByDate function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $type
	 * @param mixed $date
	 * @return void
	 */
	function getStatsByDate($key, $type, $date) {
		
		$args = array('key' => $key, 'type' => $type, 'date' => $date);
	
		return ( $this->rpc_decode( 
			$this->rpc_call('wpsd.getStatsByDate', $args) ));
	}
	
	/**
	 * getMetrics function.
	 * 
	 * @access public
	 * @return void
	 */
	function getMetrics($key) {
	
		$args = array('key' => $key);
		
		return ( $this->rpc_decode( 
			$this->rpc_call('wpsd.getMetrics', $args) ));
	}

	 /**
	 * getStatsByDateRange function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $type
	 * @param mixed $fromDate
	 * @param mixed $toDate
	 * @return void
	 */
	function getStatsByDateRange($key, $type, $fromDate, $toDate) {
		
		$args = array('key' => $key, 'type' => $type, 'from_date' => $fromDate, 'to_date' => $toDate);
	
		return ( $this->rpc_decode( 
			$this->rpc_call('wpsd.getStatsByDateRange', $args) ));
	}
	
	/**
	 * getStatsByYearAndMonth function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	function getStatsByYearAndMonth($key) {
		
		$args = array('key' => $key);
	
		return ( $this->rpc_decode( 
			$this->rpc_call('wpsd.getStatsByYearAndMonth', $args) ));
	}
	
	/**
	 * clearCache function.
	 * 
	 * @access public
	 * @return void
	 */
	function clearCache($key) {
	
		$args = array('key' => $key);
	
		return ( $this->rpc_decode( 
			$this->rpc_call('wpsd.clearCache', $args) ));
	}
	
	/**
	 * getVersion function.
	 * 
	 * @access public
	 * @return string
	 */
	function getVersion() {

		return ( $this->rpc_decode( 
			$this->rpc_call('wpsd.getVersion', array()) ));
	}
	
	/**
	 * getKey function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @return void
	 */
	function getKey($username, $password) {
		
		if('' == $username || '' == $password) { 	
			return '-1';
		}
		
		$args = array('username'=>$username, 'password'=>$password);
		
		return ( $this->rpc_decode( 
				$this->rpc_call('wpsd.getKey', $args) ));
	}
}
?>