<?php
/**
 * WPSDXbox.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDXbox extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $address_xbox_live;
	var $un;
	
	/**
	 * WPSDXbox.
	 * 
	 * @param boolean $curl
	 */
	function WPSDXbox($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdXboxUn());
		
		if('' != $this->un) {
			
			$this->address_xbox_live = 'http://live.xbox.com/en-US/profile/profile.aspx?pp=0&GamerTag=' . $this->un;
			
			$this->address = 'http://xboxapi.duncanmackenzie.net/gamertag.ashx?GamerTag=' . $this->un;
			
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
	
		//$this->values['friends'] = $this->get_count('contacts', $this->xml);
		
		//$this->set_cache('xbox_fr', $this->values['friends']);
		
		$this->values['score'] = $this->get_count('GamerScore', $this->xml);
		
		$this->set_cache('xbox_score', $this->values['score']);
		
		$this->values['rep'] = $this->get_count('Reputation', $this->xml);
		
		$this->set_cache('xbox_rep', $this->values['rep']);
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

		preg_match("@<{$type}>([0-9]+)</{$type}>@si", $data, $matches);
	
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		//$this->values['friends'] = $this->get_cache('xbox_fr');
		
		$this->values['score'] = $this->get_cache('xbox_score');
		
		$this->values['rep'] = $this->get_cache('xbox_rep');
	}
	
	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	function getFriends() {
		return $this->get('friends');
	}
	
	function getScore() {
		return $this->get('score');
	}
	
	function getReputation() {
		return $this->get('rep');
	}
	
	/**
	 * Get address.
	 * @return address
	 * @access public
	 */
	function getAddress() {
		return $this->address_xbox_live;
	}
}
?>