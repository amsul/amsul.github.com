<?php
/**
 * WPSDKoornk.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDKoornk extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDKoornk.
	 * 
	 * @param boolean $curl
	 */
	function WPSDKoornk($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdKoornkUn());
		
		if('' != $this->un) {
			
			$this->address = 'http://www.koornk.com/user/' . $this->un; 
			
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

		$this->values['friends'] = $this->get_count('Following', $this->xml);

		$this->set_cache('koornk_fr', $this->values['friends']);
		
		$this->values['followers'] = $this->get_count('Followers', $this->xml);
		
		$this->set_cache('koornk_f', $this->values['followers']);
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
		
		preg_match("@{$type}</a></span><span>([0-9]+)</span>@si", $data, $matches);
		
	//	print_r($matches);
		
		return number_format($matches[1]);
	}
		
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['friends'] = $this->get_cache('koornk_fr');
		$this->values['followers'] = $this->get_cache('koornk_f');
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
	
	/**
	 * Get followers.
	 * 
	 * @return integer followers
	 * @access public
	 */
	function getFollowers() {
		return $this->get('followers');
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