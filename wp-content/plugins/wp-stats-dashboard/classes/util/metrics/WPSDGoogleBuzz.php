<?php
/**
 * WPSDGoogleBuzz.
 * 
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDGoogleBuzz extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDBuzz.
	 * 
	 * @param boolean $curl
	 */
	function WPSDGoogleBuzz($curl = false) {
		
		parent::WPSDStats();
		
		$url = get_bloginfo('url');
				
		if('' != $url) {
			
			$url = str_replace('www.','',$url);
		
			$this->address = 'http://www.googleapis.com/buzz/v1/activities/count?url=' . $url;
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address);
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			} 
		}
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$matches = array();
		
		preg_match_all("@<thr:total>([0-9]+)</thr:total>@si", $this->xml, $matches);
		
		//print_r($matches);
		
		if(isset($matches[1][0])) {
			
			$this->values['buzz'] = number_format($matches[1][0]);
			
			$this->set_cache('google_buzz', $this->values['buzz']);	
		}
	}

	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['buzz'] = $this->get_cache('google_buzz');
	}
	
	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get the number of times a URL has been shared on Google Buzz.
	 * 
	 * @return integer buzz
	 * @access public
	 */
	function getBuzz() {
		return $this->get('buzz');
	}

	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return 'http://www.google.com/buzz';
	}
}
?>