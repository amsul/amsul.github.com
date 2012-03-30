<?php
/**
 * WPSDGoogleBot.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDGoogleBot extends WPSDStats {

	/**
	 * xml
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml;
	/**
	 * values
	 * 
	 * @var mixed
	 * @access public
	 */
	var $values;
	/**
	 * address
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $address = '';

	/**
	 * WPSDGoogleBot.
	 */
	function WPSDGoogleBot() {	
		
		parent::WPSDStats();
		
		$domain = get_bloginfo('url');			
					
		$this->address = "http://webcache.googleusercontent.com/search?q=cache:{$domain}&cd=1&hl=en&ct=clnk";
			
		if($this->isOutdated() && '' != $domain) {
			
			$this->xml = $this->fetchDataRemote($this->address, false, 1, true);
			
			$this->set();
		} 
		else {

			$this->set_cached();
		}
	}
		
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {

		preg_match_all("@on (.*)\. The @", $this->xml, $matches);
		
		$c = strip_tags(trim($matches[1][0]));
			
		$this->values['visit'] = $c;
				
		$this->set_cache('googlebot_visit', $this->values['visit'] );
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
		
		$this->values['visit'] = $this->get_cache('googlebot_visit');
	}

	/**
	 * 
	 * @param unknown_type $value
	 * @return unknown_type
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * getVisit function.
	 * 
	 * @access public
	 * @return string last date visted
	 */
	function getVisit() {
	
		return $this->get('visit');
	}

	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
	
		return $this->address;
	}
}
?>