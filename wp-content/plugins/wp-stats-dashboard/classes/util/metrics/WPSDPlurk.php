<?php
/**
 * WPSDPlurk.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDPlurk extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDPlurk.
	 * 
	 * @param boolean $curl
	 */
	function WPSDPlurk($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdPlurkUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://www.plurk.com/' . $this->un; 
			
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

		$this->values['plurks'] = $this->get_count('plurks_count', $this->xml);
		
		$this->values['responses'] = $this->get_count('response_count', $this->xml);
		
		$this->values['friends'] = $this->get_count2('num_of_friends', $this->xml);
	
		$this->values['fans'] = $this->get_count2('num_of_fans', $this->xml);
		
		$this->values['karma'] = $this->get_count3('karma', $this->xml);
		
		$this->set_cache('plurk_p', $this->values['plurks']);
		
		$this->set_cache('plurk_r', $this->values['responses']);
		
		$this->set_cache('plurk_f', $this->values['fans']);
		
		$this->set_cache('plurk_fr', $this->values['friends']);
		
		$this->set_cache('plurk_k', $this->values['karma']);
	}
	
	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return unknown_type
	 */
	function get_count3($type, $data) {
		
		preg_match("@\"{$type}\"\: ([0-9.]+)},@si", $data, $matches);
		
		return number_format($matches[1]);
	}
	/**
	 * Get count.
	 * 
	 * @param $type
	 * @param $data
	 * @return unknown_type
	 */
	function get_count2($type, $data) {
		
		preg_match("@\"{$type}\"\: (.+),@si", $data, $matches);
		
		return number_format($matches[1]);
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
		
		preg_match("@id=\"{$type}\">([0-9]+)<@si", $data, $matches);
				
		return number_format($matches[1]);
	}
		
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['friends'] = $this->get_cache('plurk_fr');
		$this->values['fans'] = $this->get_cache('plurk_f');
		$this->values['plurks'] = $this->get_cache('plurk_p');
		$this->values['responses'] = $this->get_cache('plurk_r');
		$this->values['karma'] = $this->get_cache('plurk_k');
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
	 * Get Friends.
	 * 
	 * @return integer friends
	 * @access public 
	 */
	function getFriends() {
		return $this->get('friends');
	}
	
	function getResponses() {
		return $this->get('responses');
	}
	
	function getKarma() {
		return $this->get('karma');
	}
	
	function getFans() {
		return $this->get('fans');
	}
	
	function getPlurks() {
		return $this->get('plurks');
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