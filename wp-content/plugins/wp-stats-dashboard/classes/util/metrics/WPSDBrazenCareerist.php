<?php
/**
 * WPSDBrazenCareerist.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-stats-dashboard
 */
class WPSDBrazenCareerist extends WPSDStats {

	var $xml;
	var $values;
	var $address = 'http://www.brazencareerist.com/profile/';
	var $user = '';
	
	/**
	 * WPSDBrazenCareerist.
	 * @param string $domain
	 * @param boolean $curl
	 */
	function WPSDBrazenCareerist() {
		
		parent::WPSDStats();
			
		$form = new WPSDAdminConfigForm();
			
		$this->user = trim($form->getWpsdBrazenCareeristUn());
		
		$this->address .= $this->user;
			
		if($this->isOutdated() && '' != $this->user) {
			
			$this->xml = $this->fetchDataRemote($this->address);
			
			$this->set();

		} else {

			$this->set_cached();
		}
	}
	
	/**
	 * Set fans.
	 * 
	 * @return false|integer count
	 */
 	function setFans() {
		
 		preg_match("@Fans</a> \(([0-9]+)\)@si", $this->xml, $matches);
 		
		if(null != $matches[1]) {
			
			$c = number_format($matches[1]);
				
			$this->set_cache('brazen_fans', $c);
	
			return $c;
		}
		
		return 0;
	}
	
	/**
	 * Set following.
	 * 
	 * @return string|number
	 */
	function setFollowing() {
		
		preg_match("@Following</a> \(([0-9]+)\)@si", $this->xml, $matches);
 		
		if(null != $matches[1]) {
			
			$c = number_format($matches[1]);
				
			$this->set_cache('brazen_following', $c);
	
			return $c;
		}
		
		return 0;
		
	}
	
	/**
	 * Set networks.
	 * 
	 * @return unknown_type
	 */
	function setNetworks() {
		
		preg_match("@Networks</a> \(([0-9]+)\)@si", $this->xml, $matches);
 		
		if(null != $matches[1]) {
			
			$c = number_format($matches[1]);
				
			$this->set_cache('brazen_networks', $c);
	
			return $c;
		}
		
		return 0;
	}
	
	/**
	 * Set data.
	 */
	function set() {

		$this->values['fans'] = $this->setFans();
		
		$this->values['following'] = $this->setFollowing();
		
		$this->values['networks'] = $this->setNetworks();
	}
	
	/**
	 * Get match attr.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return unknown_type
	 */
	function get_match_attr($type, $data, $match_index = 1) {
		
		preg_match("@{$type}=\"([0-9]+)\"@si", $data, $matches);

		return $matches[$match_index];
	}
	
	/**
	 * Get match tag.
	 * 
	 * @param $type
	 * @param $data
	 * @param $match_index
	 * @return unknown_type
	 */
	function get_match($type, $data, $match_index = 1) {
			
		preg_match("@<{$type}>(.*?)</{$type}>@si", $data, $matches);

		return $matches[$match_index];
	}

	/**
	 * Set cached.
	 */
	function set_cached() {
		
		$this->values['fans'] = $this->get_cache('brazen_fans');
		
		$this->values['following'] = $this->get_cache('brazen_following');
		
		$this->values['networks'] = $this->get_cache('brazen_networks');
	}

	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get_number($value){

		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}
	
	/**
	 * Get fans
	 * 
	 * @return integer fans
	 */
	function getFans() {
		return $this->values['fans'];
	}
	
	/**
	 * Get following.
	 * @return integer following
	 */
	function getFollowing() {
		return $this->values['following'];
	}
	
	/**
	 * Get networks
	 * @return integer number of networks
	 */
	function getNetworks() {
		return $this->values['networks'];
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