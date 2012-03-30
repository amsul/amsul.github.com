<?php
/**
 * WPSDBlippr.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-stats-dashboard
 */
class WPSDBlippr extends WPSDStats {

	var $xml;
	var $values;
	var $address = '';
	var $un;
	
	/**
	 * WPSDBlippr.
	 * 
	 * @param boolean $curl
	 */
	function WPSDBlippr($curl = false) {
		
		parent::WPSDStats();
		
		$form = new WPSDAdminConfigForm();
		
		$this->un = trim($form->getWpsdBlipprUn());
		
		if('' != $this->un) {
					
			$this->address = 'http://www.blippr.com/profiles/' . $this->un;
				
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
	 * 
	 * 
<a href="/profiles/daveligthart/blips" class="block "><em>0</em>reviews</a> 
<a href="/profiles/daveligthart/lists" class="block "><em>0</em>lists</a> 
<a href="/profiles/daveligthart/followers" class="block "><em>0</em>followers</a> 
<a href="/profiles/daveligthart/following" class="block "><em>1</em>following</a>  
	 */
	function set() {
		
		$this->values['blips'] = $this->getCount('reviews', $this->xml);
		
		$this->values['lists'] = $this->getCount('lists', $this->xml);
		
		$this->values['followers'] = $this->getCount('followers', $this->xml);
		
		$this->values['following'] = $this->getCount('following', $this->xml);
			
		$this->set_cache('blippr_blips', $this->values['blips']);
		
		$this->set_cache('blippr_lists', $this->values['lists']);
		
		$this->set_cache('blippr_followers', $this->values['followers']);
		
		$this->set_cache('blippr_following', $this->values['following']);			
	}
	
	/**
	 * Get count by type.
	 * 
	 * @param unknown_type $type
	 * @param unknown_type $data
	 * @return string
	 */
	function getCount($type, $data) {
		
		preg_match("@<em>([0-9]+)</em>{$type}@si", $data, $matches);
		
		return number_format($matches[1]);
	}
	
	/**
	 * Set cached.
	 */
	function set_cached() {
		$this->values['blips'] = $this->get_cache('blippr_blips');
		$this->values['lists'] = $this->get_cache('blippr_lists');
		$this->values['followers'] = $this->get_cache('blippr_followers');
		$this->values['following'] = $this->get_cache('blippr_following');
	}
	
	/**
	 * Get data.
	 * @return value
	 * @access protected
	 */
	function get($value){
		return (isset($this->values[$value]) ? $this->values[$value] : '0');
	}

	function getBlips() {
		return $this->get('blips');
	}
	
	function getLists() {
		return $this->get('lists');
	}
	
	function getFollowers() {
		return $this->get('followers');
	}
	
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