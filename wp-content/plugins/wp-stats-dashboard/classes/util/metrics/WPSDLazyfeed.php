<?php
/**
 * WPSDLazyfeed.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDLazyfeed extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDLazyfeed.
	 */
	function WPSDLazyfeed() {
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdLazyfeedUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://www.lazyfeed.com/user/' . $this->un; 
			
			if($this->isOutdated()) {
				
				$this->xml = $this->fetchDataRemote($this->address);
				
				$this->set();
				
			} else {
				
				$this->set_cached();
			}
		}
	}
	
	/**
	 * Is enabled.
	 * 
	 * @return boolean
	 */
	function isEnabled() {
		return ('' != $this->un);
	}
	
	/**
	 * Set data.
	 */
	function set() {
		
		$this->values['followers'] = $this->get_count('followers', $this->xml);
		
		$this->values['following'] = $this->get_count('following', $this->xml);
		
		$this->set_cache('lazyfeed_fs', $this->values['followers']);
		
		$this->set_cache('lazyfeed_fg', $this->values['following']);
	}
	
	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return integer count
	 * @access protected
	 */
	function get_count($type, $data) {
		preg_match("@'{$type}'\); return false;\">([0-9]+)</a>@si", $data, $matches);
			
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['followers'] = $this->get_cache('lazyfeed_fs');
		
		$this->values['following'] = $this->get_cache('lazyfeed_fg');
	}
	
	/**
	 * Get data.
	 * 
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * Get follower count.
	 * 
	 * @return integer followers
	 * @access public
	 */
	function getFollowers() {
		return $this->get('followers');
	}
	
	/**
     * Get following count.
     * 
     * @return integer following
     * @access public
	 */
	function getFollowing() {
		return $this->get('following');
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