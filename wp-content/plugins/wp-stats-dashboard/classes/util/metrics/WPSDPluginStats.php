<?php
/**
 * WPSDPluginStats.
 * @author dligthart <info@daveligthart.com>
 * @version 0.4
 * @package wp-stats-dashboard
 */
class WPSDPluginStats extends WPSDStats {

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
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		parent::__construct();
		
		$this->address = 'http://profiles.wordpress.org/users/dave.ligthart/profile/public/';
			
		if($this->isOutdated()) {
				
			$this->xml = $this->fetchDataRemote($this->address);
							
			$this->set();
			
		} else {
				
			$this->set_cached();
		}
	}
	
	/**
	 * WPSDPluginStats function.
	 * 
	 * @access public
	 * @return void
	 */
	function WPSDPluginStats() {
		
		$this->__construct();
	}
	
	/**
	 * isEnabled function.
	 * 
	 * @access public
	 * @return boolean enabled
	 */
	function isEnabled() {
		
		return true;
	}
	
	/**
	 * set function.
	 * 
	 * @access public
	 * @return void
	 */
	function set() {
		
		$this->values['downloads'] = $this->get_stat_count($this->xml);
		
		$this->set_cache('wpsd_downloads', $this->values['downloads']);
	}
	
	/**
	 * Get stat count.
	 * 
	 * @param string $data
	 * @return string number
	 */
	function get_stat_count($data) {
			
		preg_match_all("@class=\"downloads\">(.*?) downloads@", $data, $matches);
				
		$str = str_replace(',','',$matches[1][0]);
		
		settype($str, 'integer');
				
		return number_format($str);
	}
	
	/**
	 * set_cached function.
	 * 
	 * @access public
	 * @return void
	 */
	function set_cached() {
	
		$this->values['downloads'] = $this->get_cache('wpsd_downloads');
	}
	
	/**
	 * Get data.
	 * @return integer
	 * @access protected
	 */
	function get($value) {
	
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	/**
	 * Get downloads
	 * @return string
	 * @access public
	 */
	function getDownloads() {
		
		return $this->get('downloads');
	}
	
	/**
	 * Get address.
	 * @return string
	 * @access public
	 */
	function getAddress() {
	
		return $this->address;
	}
}
?>